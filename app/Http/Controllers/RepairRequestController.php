<?php

namespace App\Http\Controllers;

use App\Models\RepairRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

        return view('repair-requests.index', [
            'role' => $user->role,
            'requests' => $query->paginate(10)->withQueryString(),
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
        $validated = $request->validate([
            'device_type' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'issue_description' => ['required', 'string', 'max:2000'],
            'priority' => ['required', 'in:low,medium,high'],
            'device_image' => ['nullable', 'image', 'max:5120'],
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['status'] = RepairRequest::STATUS_PENDING;

        if ($request->hasFile('device_image')) {
            $validated['image_path'] = $request->file('device_image')->store('devices', 'public');
        }

        $repairRequest = RepairRequest::create($validated);
        $repairRequest->update(['reference' => 'RR-'.(1000 + $repairRequest->id)]);

        return redirect()
            ->route('repair-requests.show', $repairRequest)
            ->with('status', 'Repair request submitted successfully.');
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

        return view('repair-requests.show', [
            'role' => $user->role,
            'repairRequest' => $repairRequest,
            // Technicians available for assignment (admins only need this list).
            'technicians' => $user->isAdmin()
                ? User::where('role', User::ROLE_TECHNICIAN)->orderBy('name')->get()
                : collect(),
        ]);
    }

    /**
     * Assign a technician to a request (admins only).
     */
    public function assignTechnician(Request $request, RepairRequest $repairRequest): RedirectResponse
    {
        $validated = $request->validate([
            'technician_id' => [
                'required',
                Rule::exists('users', 'id')->where('role', User::ROLE_TECHNICIAN),
            ],
        ]);

        $repairRequest->technician_id = $validated['technician_id'];

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
    public function updateStatus(Request $request, RepairRequest $repairRequest): RedirectResponse
    {
        $this->authorizeWorker($request, $repairRequest);

        $validated = $request->validate([
            'status' => ['required', Rule::in(RepairRequest::STATUSES)],
        ]);

        $repairRequest->update(['status' => $validated['status']]);

        return back()->with('status', 'Repair status updated.');
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
     * Only an admin or the assigned technician may update a request.
     */
    private function authorizeWorker(Request $request, RepairRequest $repairRequest): void
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isTechnician() && $repairRequest->technician_id === $user->id) {
            return;
        }

        abort(403);
    }
}
