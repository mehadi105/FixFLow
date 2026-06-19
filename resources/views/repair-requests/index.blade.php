@php
    $requests = [
        ['id' => 'RR-1042', 'type' => 'Smartphone', 'brand_model' => 'Apple iPhone 14 Pro', 'issue' => 'Cracked screen', 'status' => 'Repairing', 'technician' => 'Mike Torres', 'date' => 'Jun 15, 2026'],
        ['id' => 'RR-1038', 'type' => 'Laptop', 'brand_model' => 'Apple MacBook Air M2', 'issue' => 'Battery not charging', 'status' => 'Diagnosing', 'technician' => 'Lisa Chen', 'date' => 'Jun 12, 2026'],
        ['id' => 'RR-1035', 'type' => 'Smartphone', 'brand_model' => 'Samsung Galaxy S23', 'issue' => 'Water damage', 'status' => 'Assigned', 'technician' => 'Mike Torres', 'date' => 'Jun 10, 2026'],
        ['id' => 'RR-1029', 'type' => 'Tablet', 'brand_model' => 'Apple iPad Air', 'issue' => 'Speaker distortion', 'status' => 'Completed', 'technician' => 'Lisa Chen', 'date' => 'Jun 05, 2026'],
        ['id' => 'RR-1022', 'type' => 'Laptop', 'brand_model' => 'Dell XPS 15', 'issue' => 'Keyboard malfunction', 'status' => 'Pending', 'technician' => 'Unassigned', 'date' => 'Jun 01, 2026'],
    ];
@endphp

<x-app-layout :role="$role ?? 'customer'">
    <x-page-header title="Repair Requests" description="View and manage all repair requests">
        <x-slot name="actions">
            <a href="{{ url('/repair-requests/create') }}" class="ff-btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                New Request
            </a>
        </x-slot>
    </x-page-header>

    <x-dashboard-card>
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center">
            <div class="ff-field flex-1">
                <label for="search" class="sr-only">Search</label>
                <input type="search" id="search" placeholder="Search by request ID, device, or issue..." class="ff-input">
            </div>
            <div class="ff-field sm:w-48">
                <label for="status-filter" class="sr-only">Filter by status</label>
                <select id="status-filter" class="ff-input">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="assigned">Assigned</option>
                    <option value="diagnosing">Diagnosing</option>
                    <option value="repairing">Repairing</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
        </div>

        <div class="ff-table-wrap">
            <table class="ff-table min-w-full">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Device Type</th>
                        <th>Brand/Model</th>
                        <th>Issue</th>
                        <th>Status</th>
                        <th>Technician</th>
                        <th>Date</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $request)
                        <tr>
                            <td class="cell-id">{{ $request['id'] }}</td>
                            <td class="cell-muted">{{ $request['type'] }}</td>
                            <td class="cell-strong">{{ $request['brand_model'] }}</td>
                            <td class="cell-truncate">{{ $request['issue'] }}</td>
                            <td><x-status-badge :status="$request['status']" /></td>
                            <td class="cell-muted">{{ $request['technician'] }}</td>
                            <td class="cell-muted">{{ $request['date'] }}</td>
                            <td class="cell-action">
                                <x-table-action-button :href="url('/repair-requests/1')">View</x-table-action-button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-dashboard-card>
</x-app-layout>
