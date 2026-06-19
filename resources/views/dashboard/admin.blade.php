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
    {{-- Overview cards --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        <x-stat-card title="Total Customers" value="156" />
        <x-stat-card title="Total Technicians" value="8" />
        <x-stat-card title="Total Repair Requests" value="40" />
        <x-stat-card title="Pending Repairs" value="8" />
        <x-stat-card title="Completed Repairs" value="11" />
        <x-stat-card title="Total Revenue" value="$33,300" />
    </div>

    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Recent requests table --}}
        <div class="lg:col-span-2">
            <x-dashboard-card title="Recent Repair Requests" description="Latest requests across all customers">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Customer</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Device</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Technician</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Priority</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($recentRequests as $request)
                                <tr class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $request['customer'] }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ $request['device'] }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ $request['technician'] }}</td>
                                    <td class="whitespace-nowrap px-4 py-3"><x-status-badge :status="$request['status']" /></td>
                                    <td class="whitespace-nowrap px-4 py-3"><x-status-badge :status="$request['priority']" /></td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right">
                                        <x-table-action-button :href="url('/repair-requests/1')">Manage</x-table-action-button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-dashboard-card>
        </div>

        {{-- Reports section --}}
        <div class="space-y-6">
            <x-dashboard-card title="Repairs by Status">
                <div class="space-y-3">
                    @foreach ($statusSummary as $item)
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="text-gray-600">{{ $item['label'] }}</span>
                                <span class="font-medium text-gray-900">{{ $item['count'] }}</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                                <div class="h-full rounded-full bg-indigo-500" style="width: {{ $item['percent'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-dashboard-card>

            <x-dashboard-card title="Monthly Revenue">
                <div class="flex items-end justify-between gap-2" style="height: 120px">
                    @foreach ($monthlyRevenue as $item)
                        <div class="flex flex-1 flex-col items-center gap-1">
                            <div class="w-full rounded-t bg-indigo-500" style="height: {{ ($item['amount'] / $maxRevenue) * 100 }}px"></div>
                            <span class="text-xs text-gray-500">{{ $item['month'] }}</span>
                        </div>
                    @endforeach
                </div>
                <p class="mt-3 text-center text-sm text-gray-500">Jun total: <span class="font-semibold text-gray-900">$7,100</span></p>
            </x-dashboard-card>
        </div>
    </div>

    {{-- Quick actions --}}
    <x-dashboard-card title="Quick Actions">
        <div class="flex flex-wrap gap-3">
            <a href="{{ url('/repair-requests') }}" class="ff-btn-primary">Manage Requests</a>
            <a href="#" class="ff-btn-secondary">Assign Technician</a>
            <a href="{{ url('/invoices') }}" class="ff-btn-secondary">Create Invoice</a>
            <a href="{{ url('/reports') }}" class="ff-btn-secondary">View Reports</a>
        </div>
    </x-dashboard-card>
</x-app-layout>
