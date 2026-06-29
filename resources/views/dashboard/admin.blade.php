@php
    $maxRevenue = max(array_map(fn ($m) => $m['amount'], $monthlyRevenue)) ?: 1;
    $statusTotal = array_sum($statusCounts);
@endphp

<x-app-layout :role="'admin'">
    <x-page-header title="Admin Dashboard" description="Overview of customers, repairs, and revenue" />

    <div class="ff-stats-grid-wide">
        <x-stat-card title="Total Customers" :value="$stats['customers']" />
        <x-stat-card title="Total Technicians" :value="$stats['technicians']" />
        <x-stat-card title="Total Repair Requests" :value="$stats['total']" />
        <x-stat-card title="Pending Repairs" :value="$stats['pending']" />
        <x-stat-card title="Completed Repairs" :value="$stats['completed']" />
        <x-stat-card title="Total Revenue" :value="'$'.number_format($stats['revenue'], 2)" />
    </div>

    <div class="ff-grid-sidebar mb-6">
        <div class="lg:col-span-2">
            <x-dashboard-card title="Recent Repair Requests" description="Latest requests across all customers">
                <div class="ff-table-wrap">
                    <table class="ff-table min-w-full">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Device</th>
                                <th>Technician</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentRequests as $request)
                                <tr>
                                    <td class="cell-strong">{{ $request->customer->name }}</td>
                                    <td class="cell-muted">{{ $request->device_label }}</td>
                                    <td class="cell-muted">{{ $request->technician?->name ?? 'Unassigned' }}</td>
                                    <td><x-status-badge :status="$request->status" /></td>
                                    <td><x-status-badge :status="$request->priority" /></td>
                                    <td class="cell-action">
                                        <x-table-action-button :href="route('repair-requests.show', $request)">Manage</x-table-action-button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">No repair requests yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-dashboard-card>
        </div>

        <div class="ff-section">
            <x-dashboard-card title="Repairs by Status">
                <div class="space-y-4">
                    @foreach ($statusCounts as $label => $count)
                        <x-progress-bar
                            :label="ucfirst($label)"
                            :value="$count"
                            :percent="$statusTotal > 0 ? round(($count / $statusTotal) * 100) : 0"
                        />
                    @endforeach
                </div>
            </x-dashboard-card>

            <x-dashboard-card title="Monthly Revenue" description="Paid invoices over the last 6 months">
                <div class="flex h-32 items-end justify-between gap-2">
                    @foreach ($monthlyRevenue as $item)
                        <div class="flex flex-1 flex-col items-center gap-1">
                            <div class="w-full rounded-t ff-progress-bar" style="height: {{ ($item['amount'] / $maxRevenue) * 100 }}px"></div>
                            <span class="text-xs text-slate-500">{{ $item['label'] }}</span>
                        </div>
                    @endforeach
                </div>
                <p class="mt-4 text-center text-sm text-slate-500">
                    {{ end($monthlyRevenue)['label'] }} total: <span class="font-semibold text-slate-900">${{ number_format(end($monthlyRevenue)['amount'], 2) }}</span>
                </p>
            </x-dashboard-card>
        </div>
    </div>

    <x-dashboard-card title="Quick Actions">
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('repair-requests.index') }}" class="ff-btn-primary">Manage Requests</a>
            <a href="{{ route('repair-requests.index') }}" class="ff-btn-secondary">Assign Technician</a>
            <a href="{{ route('invoices.create') }}" class="ff-btn-secondary">Create Invoice</a>
            <a href="{{ url('/reports') }}" class="ff-btn-secondary">View Reports</a>
        </div>
    </x-dashboard-card>
</x-app-layout>
