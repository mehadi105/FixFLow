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
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Invoice No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Repair Request</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Total Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Payment Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Date</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($invoices as $invoice)
                        <tr class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-indigo-600">{{ $invoice['no'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900">{{ $invoice['customer'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ $invoice['repair'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $invoice['amount'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3"><x-status-badge :status="$invoice['status']" /></td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $invoice['date'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                <x-table-action-button :href="url('/invoices/1')">View</x-table-action-button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-dashboard-card>
</x-app-layout>
