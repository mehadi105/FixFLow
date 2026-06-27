<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\RepairRequest;
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

        // Customers only see their own invoices.
        if ($user->isCustomer()) {
            $query->where('user_id', $user->id);
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
            'payment_status' => ['required', 'in:unpaid,paid'],
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

        $invoice->load(['customer', 'repairRequest.technician']);

        return view('invoices.show', [
            'role' => $user->role,
            'invoice' => $invoice,
        ]);
    }

    /**
     * Toggle an invoice between paid and unpaid (admins only).
     */
    public function markPaid(Request $request, Invoice $invoice): RedirectResponse
    {
        $invoice->update([
            'payment_status' => $invoice->isPaid() ? Invoice::STATUS_UNPAID : Invoice::STATUS_PAID,
        ]);

        return back()->with('status', 'Payment status updated.');
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
