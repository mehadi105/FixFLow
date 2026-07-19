<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\RepairRequest;
use App\Models\User;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RepairRequestController extends Controller
{
    /**
     * List repair requests scoped to the current user's role.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = RepairRequest::query()
            ->with(['customer', 'technician'])
            ->latest();

        // Customers only see their own requests; technicians see assigned ones.
        if ($user->isCustomer()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isTechnician()) {
            $query->where('technician_id', $user->id);
        }

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('device_type', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('issue_description', 'like', "%{$search}%");
            });
        }

        if ($status = $request->string('status')->trim()->value()) {
            $query->where('status', $status);
        }

        $requests = $query->paginate(10)->withQueryString();
        $unreadCounts = Message::unreadCountsByRepairRequestForUser(
            $user,
            $requests->getCollection()->pluck('id')
        );

        return view('repair-requests.index', [
            'role' => $user->role,
            'requests' => $requests,
            'unreadCounts' => $unreadCounts,
            'search' => $search ?? '',
            'status' => $status ?? '',
        ]);
    }

    /**
     * Show the create form (customers only).
     */
    public function create(Request $request): View
    {
        return view('repair-requests.create', [
            'role' => $request->user()->role,
        ]);
    }

    /**
     * Store a new repair request (customers only).
     */
    public function store(Request $request): RedirectResponse
    {
        $this->assertDeviceImageUploaded($request);

        $validated = $request->validate([
            'device_type' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'issue_description' => ['required', 'string', 'max:2000'],
            'priority' => ['required', 'in:low,medium,high'],
            'device_image' => ['nullable', 'file', 'image', 'max:2048'],
        ]);

        unset($validated['device_image']);

        $validated['user_id'] = $request->user()->id;
        $validated['status'] = RepairRequest::STATUS_PENDING;
        $validated['image_path'] = $request->file('device_image')?->store('devices', 'public');

        $repairRequest = RepairRequest::create($validated);
        $repairRequest->update(['reference' => 'RR-'.(1000 + $repairRequest->id)]);

        return redirect()
            ->route('repair-requests.show', $repairRequest)
            ->with('status', 'Repair request submitted successfully.');
    }

    /**
     * Surface PHP upload failures that Laravel would otherwise skip silently.
     */
    private function assertDeviceImageUploaded(Request $request): void
    {
        $upload = $_FILES['device_image'] ?? null;

        if (! is_array($upload) || ($upload['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return;
        }

        $message = match ($upload['error']) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'The device image is too large. Please upload a file under 2 MB.',
            UPLOAD_ERR_PARTIAL => 'The device image only partially uploaded. Please try again.',
            default => 'The device image failed to upload. Please try again.',
        };

        if ($upload['error'] !== UPLOAD_ERR_OK) {
            throw ValidationException::withMessages([
                'device_image' => $message,
            ]);
        }
    }

    /**
     * Show a single repair request.
     */
    public function show(Request $request, RepairRequest $repairRequest): View
    {
        $user = $request->user();

        // Customers and technicians may only view their own related requests.
        if ($user->isCustomer() && $repairRequest->user_id !== $user->id) {
            abort(403);
        }

        if ($user->isTechnician() && $repairRequest->technician_id !== $user->id) {
            abort(403);
        }

        $repairRequest->load(['customer', 'technician', 'invoice', 'warranty']);

        $canChat = $repairRequest->hasChatParticipant($user);

        $invoices = app(InvoiceService::class);

        return view('repair-requests.show', [
            'role' => $user->role,
            'repairRequest' => $repairRequest,
            'canChat' => $canChat,
            'suggestedServiceCharge' => $invoices->suggestServiceCharge(),
            'suggestedPartsCost' => $invoices->estimatePartsCost($repairRequest),
            'suggestedDiagnosisFee' => $invoices->suggestDiagnosisFee(),
            // Technicians available for assignment (admins only need this list).
            'technicians' => $user->isAdmin()
                ? User::approvedTechnicians()->orderBy('name')->get()
                : collect(),
        ]);
    }

    /**
     * Assign a technician to a request (admins only).
     */
    public function assignTechnician(Request $request, RepairRequest $repairRequest): RedirectResponse
    {
        $validated = $request->validate([
            'technician_id' => ['required', 'exists:users,id'],
        ]);

        $technician = User::approvedTechnicians()->find($validated['technician_id']);

        if (! $technician) {
            return back()->withErrors([
                'technician_id' => 'Select an approved technician.',
            ]);
        }

        $repairRequest->technician_id = $technician->id;

        // Move a brand-new request forward once it has an owner.
        if ($repairRequest->status === RepairRequest::STATUS_PENDING) {
            $repairRequest->status = RepairRequest::STATUS_ASSIGNED;
        }

        $repairRequest->save();

        return back()->with('status', 'Technician assigned successfully.');
    }

    /**
     * Update the repair status (assigned technician or admin).
     */
    public function updateStatus(Request $request, RepairRequest $repairRequest, InvoiceService $invoices): RedirectResponse
    {
        $this->authorizeWorker($request, $repairRequest);

        $allowed = $repairRequest->availableStatusTransitions();

        $validated = $request->validate([
            'status' => ['required', Rule::in($allowed)],
        ]);

        $repairRequest->update(['status' => $validated['status']]);

        if ($validated['status'] === RepairRequest::STATUS_COMPLETED) {
            $invoices->ensureInvoiceForCompletedRepair($repairRequest->fresh());
        }

        $message = $validated['status'] === RepairRequest::STATUS_COMPLETED
            ? 'Repair marked completed. A draft invoice has been generated for admin review.'
            : 'Repair status updated.';

        return back()->with('status', $message);
    }

    /**
     * Save diagnosis notes (assigned technician or admin).
     */
    public function updateDiagnosis(Request $request, RepairRequest $repairRequest): RedirectResponse
    {
        $this->authorizeWorker($request, $repairRequest);

        $validated = $request->validate([
            'diagnosis_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $repairRequest->update(['diagnosis_notes' => $validated['diagnosis_notes']]);

        return back()->with('status', 'Diagnosis notes saved.');
    }

    /**
     * Submit (or revise) a post-diagnosis repair quote for customer approval.
     */
    public function submitQuote(Request $request, RepairRequest $repairRequest, InvoiceService $invoices): RedirectResponse
    {
        $this->authorizeWorker($request, $repairRequest);

        if (! in_array($repairRequest->status, [
            RepairRequest::STATUS_DIAGNOSING,
            RepairRequest::STATUS_QUOTED,
        ], true)) {
            return back()->withErrors([
                'quote' => 'Quotes can only be sent while the repair is in diagnosis or awaiting customer decision.',
            ]);
        }

        if (! filled($repairRequest->diagnosis_notes)) {
            return back()->withErrors([
                'diagnosis_notes' => 'Save diagnosis notes before sending a quote.',
            ]);
        }

        $validated = $request->validate([
            'quote_service_charge' => ['required', 'numeric', 'min:0'],
            'quote_parts_cost' => ['required', 'numeric', 'min:0'],
            'quote_discount' => ['required', 'numeric', 'min:0'],
            'diagnosis_fee' => ['required', 'numeric', 'min:0'],
            'quote_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $repairRequest->update([
            'quote_service_charge' => $validated['quote_service_charge'],
            'quote_parts_cost' => $validated['quote_parts_cost'],
            'quote_discount' => $validated['quote_discount'],
            'diagnosis_fee' => $validated['diagnosis_fee'],
            'quote_notes' => $validated['quote_notes'] ?? null,
            'quoted_at' => now(),
            'quote_responded_at' => null,
            'status' => RepairRequest::STATUS_QUOTED,
        ]);

        return back()->with(
            'status',
            'Quote sent to the customer. Repair will continue only if they approve. Suggested total: $'
            .number_format($repairRequest->fresh()->quoteTotal(), 2).'.'
        );
    }

    /**
     * Customer approves the quote so repair work can continue.
     */
    public function approveQuote(Request $request, RepairRequest $repairRequest): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isCustomer() || $repairRequest->user_id !== $user->id) {
            abort(403);
        }

        if (! $repairRequest->canRespondToQuote()) {
            return back()->withErrors([
                'quote' => 'There is no quote waiting for your decision.',
            ]);
        }

        $repairRequest->update([
            'status' => RepairRequest::STATUS_REPAIRING,
            'quote_responded_at' => now(),
        ]);

        return back()->with('status', 'Quote approved. Our technician will continue the repair.');
    }

    /**
     * Customer declines the quote and is billed only the diagnosis fee.
     */
    public function declineQuote(Request $request, RepairRequest $repairRequest, InvoiceService $invoices): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isCustomer() || $repairRequest->user_id !== $user->id) {
            abort(403);
        }

        if (! $repairRequest->canRespondToQuote()) {
            return back()->withErrors([
                'quote' => 'There is no quote waiting for your decision.',
            ]);
        }

        $repairRequest->update([
            'status' => RepairRequest::STATUS_DECLINED,
            'quote_responded_at' => now(),
        ]);

        $invoice = $invoices->createDiagnosisFeeInvoice($repairRequest->fresh());

        return redirect()
            ->route('invoices.show', $invoice)
            ->with(
                'status',
                'Quote declined. Please pay the diagnosis fee ($'.number_format((float) $invoice->total, 2)
                .'), then choose how to get your device back.'
            );
    }

    /**
     * Customer chooses pickup at service center or home delivery after payment.
     */
    public function chooseFulfillment(Request $request, RepairRequest $repairRequest): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isCustomer() || $repairRequest->user_id !== $user->id) {
            abort(403);
        }

        if (! $repairRequest->canChooseFulfillment()) {
            return back()->withErrors([
                'fulfillment' => 'Pay your invoice first, then choose how to receive your device.',
            ]);
        }

        $validated = $request->validate([
            'fulfillment_method' => ['required', Rule::in([
                RepairRequest::FULFILLMENT_METHOD_PICKUP,
                RepairRequest::FULFILLMENT_METHOD_DELIVERY,
            ])],
            'delivery_address' => [
                Rule::requiredIf($request->input('fulfillment_method') === RepairRequest::FULFILLMENT_METHOD_DELIVERY),
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        $status = $validated['fulfillment_method'] === RepairRequest::FULFILLMENT_METHOD_PICKUP
            ? RepairRequest::FULFILLMENT_READY_FOR_PICKUP
            : RepairRequest::FULFILLMENT_OUT_FOR_DELIVERY;

        $repairRequest->update([
            'fulfillment_method' => $validated['fulfillment_method'],
            'delivery_address' => $validated['fulfillment_method'] === RepairRequest::FULFILLMENT_METHOD_DELIVERY
                ? $validated['delivery_address']
                : null,
            'fulfillment_status' => $status,
        ]);

        $message = $validated['fulfillment_method'] === RepairRequest::FULFILLMENT_METHOD_PICKUP
            ? 'Pickup selected. Your device will be ready at the FixFlow service center.'
            : 'Home delivery scheduled. We will deliver your device to the address provided.';

        return back()->with('status', $message);
    }

    /**
     * Admin confirms the device was picked up or delivered.
     */
    public function completeFulfillment(Request $request, RepairRequest $repairRequest): RedirectResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        if (! $repairRequest->canCompleteFulfillment()) {
            return back()->withErrors([
                'fulfillment' => 'This repair is not waiting for pickup or delivery confirmation.',
            ]);
        }

        $repairRequest->update([
            'fulfillment_status' => RepairRequest::FULFILLMENT_FULFILLED,
        ]);

        return back()->with('status', 'Device handover marked as completed.');
    }

    /**
     * Only an admin or the assigned technician may update a request.
     */
    private function authorizeWorker(Request $request, RepairRequest $repairRequest): void
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isTechnician() && $repairRequest->technician_id === $user->id && $user->isApprovedTechnician()) {
            return;
        }

        abort(403);
    }
}
