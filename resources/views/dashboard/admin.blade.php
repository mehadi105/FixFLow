@php
    $recentRequests = [
        ['customer' => 'Sarah Ahmed', 'device' => 'iPhone 14 Pro', 'technician' => 'Mike Torres', 'status' => 'Repairing', 'priority' => 'High'],
        ['customer' => 'James Wilson', 'device' => 'MacBook Pro 16"', 'technician' => 'Lisa Chen', 'status' => 'Diagnosing', 'priority' => 'Medium'],
        ['customer' => 'Emily Davis', 'device' => 'Samsung S24 Ultra', 'technician' => 'Unassigned', 'status' => 'Pending', 'priority' => 'High'],
        ['customer' => 'Robert Kim', 'device' => 'HP Pavilion', 'technician' => 'Mike Torres', 'status' => 'Assigned', 'priority' => 'Low'],
        ['customer' => 'Anna Martinez', 'device' => 'iPad Pro 12.9"', 'technician' => 'Lisa Chen', 'status' => 'Completed', 'priority' => 'Medium'],
    ];

    $statusSummary = [
        ['label' => 'Pending', 'count' => 8, 'percent' => 20],
        ['label' => 'Assigned', 'count' => 6, 'percent' => 15],
        ['label' => 'Diagnosing', 'count' => 5, 'percent' => 12],
        ['label' => 'Repairing', 'count' => 10, 'percent' => 25],
        ['label' => 'Completed', 'count' => 11, 'percent' => 28],
    ];

    $monthlyRevenue = [
        ['month' => 'Jan', 'amount' => 4200],
        ['month' => 'Feb', 'amount' => 5100],
        ['month' => 'Mar', 'amount' => 4800],
        ['month' => 'Apr', 'amount' => 6200],
        ['month' => 'May', 'amount' => 5900],
        ['month' => 'Jun', 'amount' => 7100],
    ];
    $maxRevenue = max(array_column($monthlyRevenue, 'amount'));
@endphp

<x-app-layout :role="'admin'">
    <x-page-header title="Admin Dashboard" description="Overview of customers, repairs, and revenue" />

    <div class="ff-stats-grid-wide">
        <x-stat-card title="Total Customers" value="156" />
        <x-stat-card title="Total Technicians" value="8" />
        <x-stat-card title="Total Repair Requests" value="40" />
        <x-stat-card title="Pending Repairs" value="8" />
        <x-stat-card title="Completed Repairs" value="11" />
        <x-stat-card title="Total Revenue" value="$33,300" />
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
                            @foreach ($recentRequests as $request)
                                <tr>
                                    <td class="cell-strong">{{ $request['customer'] }}</td>
                                    <td class="cell-muted">{{ $request['device'] }}</td>
                                    <td class="cell-muted">{{ $request['technician'] }}</td>
                                    <td><x-status-badge :status="$request['status']" /></td>
                                    <td><x-status-badge :status="$request['priority']" /></td>
                                    <td class="cell-action">
                                        <x-table-action-button :href="url('/repair-requests/1')">Manage</x-table-action-button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-dashboard-card>
        </div>

        <div class="ff-section">
            <x-dashboard-card title="Repairs by Status">
                <div class="space-y-4">
                    @foreach ($statusSummary as $item)
                        <x-progress-bar :label="$item['label']" :value="$item['count']" :percent="$item['percent']" />
                    @endforeach
                </div>
            </x-dashboard-card>

            <x-dashboard-card title="Monthly Revenue">
                <div class="flex h-32 items-end justify-between gap-2">
                    @foreach ($monthlyRevenue as $item)
                        <div class="flex flex-1 flex-col items-center gap-1">
                            <div class="w-full rounded-t ff-progress-bar" style="height: {{ ($item['amount'] / $maxRevenue) * 100 }}px"></div>
                            <span class="text-xs text-slate-500">{{ $item['month'] }}</span>
                        </div>
                    @endforeach
                </div>
                <p class="mt-4 text-center text-sm text-slate-500">
                    Jun total: <span class="font-semibold text-slate-900">$7,100</span>
                </p>
            </x-dashboard-card>
        </div>
    </div>

    <x-dashboard-card title="Quick Actions">
        <div class="flex flex-wrap gap-3">
            <a href="{{ url('/repair-requests') }}" class="ff-btn-primary">Manage Requests</a>
            <a href="#" class="ff-btn-secondary">Assign Technician</a>
            <a href="{{ url('/invoices') }}" class="ff-btn-secondary">Create Invoice</a>
            <a href="{{ url('/reports') }}" class="ff-btn-secondary">View Reports</a>
        </div>
    </x-dashboard-card>
</x-app-layout>
