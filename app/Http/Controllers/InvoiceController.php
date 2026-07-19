<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\RepairRequest;
use App\Services\InvoiceService;
use App\Services\StripePaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * List invoices scoped to the current user's role.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Invoice::query()
            ->with(['customer', 'repairRequest'])
            ->latest();

        // Customers only see sent or paid invoices — not drafts under review.
        if ($user->isCustomer()) {
            $query->where('user_id', $user->id)
                ->whereIn('payment_status', [Invoice::STATUS_UNPAID, Invoice::STATUS_PAID]);
        }

        return view('invoices.index', [
            'role' => $user->role,
            'invoices' => $query->paginate(10),
        ]);
    }

    /**
     * Show the invoice creation form (admins only).
     */
    public function create(Request $request): View
    {
        return view('invoices.create', [
            'role' => $request->user()->role,
            'repairRequests' => $this->billableRequests(),
            'selectedRequestId' => $request->integer('repair_request_id') ?: null,
        ]);
    }

    /**
     * Store a new invoice (admins only).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'repair_request_id' => ['required', 'exists:repair_requests,id', 'unique:invoices,repair_request_id'],
            'service_charge' => ['required', 'numeric', 'min:0'],
            'parts_cost' => ['required', 'numeric', 'min:0'],
            'discount' => ['required', 'numeric', 'min:0'],
            'payment_status' => ['required', 'in:draft,unpaid,paid'],
        ]);

        $repairRequest = RepairRequest::findOrFail($validated['repair_request_id']);

        $total = max(0, $validated['service_charge'] + $validated['parts_cost'] - $validated['discount']);

        $invoice = Invoice::create([
            'repair_request_id' => $repairRequest->id,
            'user_id' => $repairRequest->user_id,
            'service_charge' => $validated['service_charge'],
            'parts_cost' => $validated['parts_cost'],
            'discount' => $validated['discount'],
            'total' => $total,
            'payment_status' => $validated['payment_status'],
        ]);

        $invoice->update(['invoice_number' => 'INV-'.now()->year.'-'.str_pad((string) $invoice->id, 4, '0', STR_PAD_LEFT)]);

        if (in_array($repairRequest->status, [
            RepairRequest::STATUS_COMPLETED,
            RepairRequest::STATUS_DECLINED,
        ], true)) {
            $invoices = app(InvoiceService::class);
            if ($invoice->isDraft()) {
                $repairRequest->update(['fulfillment_status' => RepairRequest::FULFILLMENT_AWAITING_INVOICE]);
            } elseif ($invoice->isPayable()) {
                $repairRequest->update(['fulfillment_status' => RepairRequest::FULFILLMENT_AWAITING_PAYMENT]);
            } elseif ($invoice->isPaid()) {
                $invoices->handleInvoicePaid($invoice);
            }
        }

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('status', 'Invoice created successfully.');
    }

    /**
     * Show a single invoice.
     */
    public function show(Request $request, Invoice $invoice): View
    {
        $user = $request->user();

        // Customers may only view their own invoices.
        if ($user->isCustomer() && $invoice->user_id !== $user->id) {
            abort(403);
        }

        // Customers cannot view draft invoices until admin sends them.
        if ($user->isCustomer() && $invoice->isDraft()) {
            return redirect()
                ->route('repair-requests.show', $invoice->repair_request_id)
                ->with('status', 'Your invoice is being reviewed by our team. You will be notified when payment is due.');
        }

        $invoice->load(['customer', 'repairRequest.technician']);

        return view('invoices.show', [
            'role' => $user->role,
            'invoice' => $invoice,
            'stripeEnabled' => StripePaymentService::isConfigured(),
        ]);
    }

    /**
     * Update draft invoice amounts before sending to the customer.
     */
    public function update(Request $request, Invoice $invoice, InvoiceService $invoices): RedirectResponse
    {
        if (! $invoice->isDraft()) {
            return back()->withErrors([
                'invoice' => 'Only draft invoices can be edited. Sent invoices are locked.',
            ]);
        }

        $validated = $request->validate([
            'service_charge' => ['required', 'numeric', 'min:0'],
            'parts_cost' => ['required', 'numeric', 'min:0'],
            'discount' => ['required', 'numeric', 'min:0'],
        ]);

        $total = $invoices->recalculateTotal(
            $invoice,
            (float) $validated['service_charge'],
            (float) $validated['parts_cost'],
            (float) $validated['discount'],
        );

        $invoice->update([
            'service_charge' => $validated['service_charge'],
            'parts_cost' => $validated['parts_cost'],
            'discount' => $validated['discount'],
            'total' => $total,
        ]);

        return back()->with('status', 'Draft invoice updated. Send it to the customer when ready.');
    }

    /**
     * Send a reviewed draft invoice to the customer for payment.
     */
    public function send(Request $request, Invoice $invoice, InvoiceService $invoices): RedirectResponse
    {
        if (! $invoice->isDraft()) {
            return back()->withErrors([
                'invoice' => 'This invoice has already been sent or paid.',
            ]);
        }

        $invoices->sendInvoiceToCustomer($invoice);

        return back()->with('status', 'Invoice sent to customer. They can now pay online or at the service center.');
    }

    /**
     * Toggle an invoice between paid and unpaid (admins only).
     */
    public function markPaid(Request $request, Invoice $invoice, InvoiceService $invoices): RedirectResponse
    {
        if ($invoice->isPaid()) {
            $invoice->update([
                'payment_status' => Invoice::STATUS_UNPAID,
                'payment_method' => null,
                'paid_at' => null,
            ]);

            $invoice->repairRequest?->update([
                'fulfillment_status' => RepairRequest::FULFILLMENT_AWAITING_PAYMENT,
                'fulfillment_method' => null,
                'delivery_address' => null,
            ]);
        } else {
            if ($invoice->isDraft()) {
                $invoices->sendInvoiceToCustomer($invoice->fresh());
                $invoice->refresh();
            }

            $invoice->update([
                'payment_status' => Invoice::STATUS_PAID,
                'payment_method' => 'manual',
                'paid_at' => now(),
            ]);

            if ($invoice->repairRequest) {
                $invoices->handleInvoicePaid($invoice);
            }
        }

        return back()->with('status', 'Payment status updated.');
    }

    /**
     * Delete only an unsent draft invoice and return to its repair.
     */
    public function destroy(Invoice $invoice): RedirectResponse
    {
        if (! $invoice->isDraft()) {
            return back()->withErrors([
                'invoice' => 'Only unsent draft invoices can be deleted.',
            ]);
        }

        $repairRequest = $invoice->repairRequest;

        // Eloquent delete example; the paid/sent states above remain protected.
        $invoice->delete();

        if ($repairRequest?->status === RepairRequest::STATUS_COMPLETED) {
            $repairRequest->update([
                'fulfillment_status' => RepairRequest::FULFILLMENT_AWAITING_INVOICE,
            ]);
        }

        return redirect()
            ->route('repair-requests.show', $repairRequest)
            ->with('status', 'Draft invoice deleted. You can create a replacement invoice.');
    }

    /**
     * Repair requests that do not yet have an invoice.
     */
    private function billableRequests()
    {
        return RepairRequest::query()
            ->with('customer')
            ->whereDoesntHave('invoice')
            ->latest()
            ->get();
    }
}
