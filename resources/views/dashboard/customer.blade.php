@php
    $recentRequests = [
        ['id' => 'RR-1042', 'device' => 'iPhone 14 Pro', 'issue' => 'Cracked screen, touch not responding', 'status' => 'Repairing', 'date' => 'Jun 15, 2026'],
        ['id' => 'RR-1038', 'device' => 'MacBook Air M2', 'issue' => 'Battery not charging', 'status' => 'Diagnosing', 'date' => 'Jun 12, 2026'],
        ['id' => 'RR-1025', 'device' => 'Samsung Galaxy S23', 'issue' => 'Water damage', 'status' => 'Assigned', 'date' => 'Jun 08, 2026'],
        ['id' => 'RR-1019', 'device' => 'iPad Air', 'issue' => 'Speaker distortion', 'status' => 'Completed', 'date' => 'May 28, 2026'],
        ['id' => 'RR-1011', 'device' => 'Dell XPS 15', 'issue' => 'Keyboard keys stuck', 'status' => 'Pending', 'date' => 'May 20, 2026'],
    ];
@endphp

<x-app-layout :role="'customer'">
    <x-hero-banner
        title="Welcome back, John"
        description="Track repair requests, invoices, and warranty coverage from your dashboard."
    >
        <x-slot name="actions">
            <a href="{{ url('/repair-requests/create') }}" class="ff-btn-inverse">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                New Repair Request
            </a>
        </x-slot>
    </x-hero-banner>

    <div class="ff-stats-grid">
        <x-stat-card title="Total Requests" value="12" />
        <x-stat-card title="Pending Repairs" value="3" />
        <x-stat-card title="Completed Repairs" value="8" />
        <x-stat-card title="Active Warranty" value="2" />
    </div>

    <x-dashboard-card title="Recent Repair Requests" description="Your latest submitted repair requests">
        <div class="ff-table-wrap">
            <table class="ff-table min-w-full">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Device</th>
                        <th>Issue</th>
                        <th>Status</th>
                        <th>Submitted Date</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentRequests as $request)
                        <tr>
                            <td class="cell-id">{{ $request['id'] }}</td>
                            <td class="cell-strong">{{ $request['device'] }}</td>
                            <td class="cell-truncate">{{ $request['issue'] }}</td>
                            <td><x-status-badge :status="$request['status']" /></td>
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
