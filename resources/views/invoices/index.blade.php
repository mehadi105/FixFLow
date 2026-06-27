<x-app-layout :role="$role ?? 'customer'">
    <x-page-header title="Invoices" description="View and manage repair invoices">
        @if (auth()->user()->isAdmin())
            <x-slot name="actions">
                <a href="{{ route('invoices.create') }}" class="ff-btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    New Invoice
                </a>
            </x-slot>
        @endif
    </x-page-header>

    @if (session('status'))
        <div class="mb-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700 ring-1 ring-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    <x-dashboard-card>
        <div class="ff-table-wrap">
            <table class="ff-table min-w-full">
                <thead>
                    <tr>
                        <th>Invoice No</th>
                        <th>Customer</th>
                        <th>Repair Request</th>
                        <th>Total Amount</th>
                        <th>Payment Status</th>
                        <th>Date</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $invoice)
                        <tr>
                            <td class="cell-id">{{ $invoice->invoice_number }}</td>
                            <td class="cell-strong">{{ $invoice->customer->name }}</td>
                            <td class="cell-muted">{{ $invoice->repairRequest->reference }}</td>
                            <td class="cell-strong">${{ number_format($invoice->total, 2) }}</td>
                            <td><x-status-badge :status="$invoice->payment_status" /></td>
                            <td class="cell-muted">{{ $invoice->created_at->format('M d, Y') }}</td>
                            <td class="cell-action">
                                <x-table-action-button :href="route('invoices.show', $invoice)">View</x-table-action-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">No invoices found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($invoices->hasPages())
            <div class="mt-6">{{ $invoices->links() }}</div>
        @endif
    </x-dashboard-card>
</x-app-layout>
