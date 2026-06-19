@php
    $invoices = [
        ['no' => 'INV-2026-0095', 'customer' => 'Sarah Ahmed', 'repair' => 'RR-1042', 'amount' => '$189.00', 'status' => 'Paid', 'date' => 'Jun 18, 2026'],
        ['no' => 'INV-2026-0092', 'customer' => 'James Wilson', 'repair' => 'RR-1038', 'amount' => '$320.00', 'status' => 'Unpaid', 'date' => 'Jun 16, 2026'],
        ['no' => 'INV-2026-0088', 'customer' => 'Emily Davis', 'repair' => 'RR-1035', 'amount' => '$75.00', 'status' => 'Paid', 'date' => 'Jun 14, 2026'],
        ['no' => 'INV-2026-0085', 'customer' => 'Robert Kim', 'repair' => 'RR-1029', 'amount' => '$145.00', 'status' => 'Overdue', 'date' => 'Jun 10, 2026'],
        ['no' => 'INV-2026-0081', 'customer' => 'Anna Martinez', 'repair' => 'RR-1022', 'amount' => '$210.00', 'status' => 'Paid', 'date' => 'Jun 05, 2026'],
    ];
@endphp

<x-app-layout :role="$role ?? 'customer'">
    <x-page-header title="Invoices" description="View and manage repair invoices" />

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
                    @foreach ($invoices as $invoice)
                        <tr>
                            <td class="cell-id">{{ $invoice['no'] }}</td>
                            <td class="cell-strong">{{ $invoice['customer'] }}</td>
                            <td class="cell-muted">{{ $invoice['repair'] }}</td>
                            <td class="cell-strong">{{ $invoice['amount'] }}</td>
                            <td><x-status-badge :status="$invoice['status']" /></td>
                            <td class="cell-muted">{{ $invoice['date'] }}</td>
                            <td class="cell-action">
                                <x-table-action-button :href="url('/invoices/1')">View</x-table-action-button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-dashboard-card>
</x-app-layout>
