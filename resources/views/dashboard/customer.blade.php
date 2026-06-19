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
    {{-- Welcome card --}}
    <div class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-indigo-950 to-blue-950 p-8 shadow-2xl shadow-indigo-900/20 sm:p-10">
        <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full bg-indigo-500/20 blur-3xl"></div>
        <div class="absolute -bottom-8 left-1/3 h-32 w-32 rounded-full bg-blue-500/15 blur-2xl"></div>
        <div class="relative">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-300">Dashboard</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-white sm:text-4xl">Welcome back, John</h1>
            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-300 sm:text-base">Track repair requests, invoices, and warranty coverage — all in one refined workspace.</p>
            <a href="{{ url('/repair-requests/create') }}" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-indigo-700 shadow-lg transition-colors hover:bg-indigo-50">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                New Repair Request
            </a>
        </div>
    </div>

    {{-- Stats cards --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card title="Total Requests" value="12" />
        <x-stat-card title="Pending Repairs" value="3" />
        <x-stat-card title="Completed Repairs" value="8" />
        <x-stat-card title="Active Warranty" value="2" />
    </div>

    {{-- Recent repair requests --}}
    <x-dashboard-card title="Recent Repair Requests" description="Your latest submitted repair requests">
        <div class="overflow-x-auto">
            <table class="ff-table min-w-full">
                <thead>
                    <tr class="border-b border-slate-100">
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
                            <td class="whitespace-nowrap font-semibold text-indigo-600">{{ $request['id'] }}</td>
                            <td class="whitespace-nowrap font-medium text-slate-900">{{ $request['device'] }}</td>
                            <td class="max-w-xs truncate text-slate-500">{{ $request['issue'] }}</td>
                            <td class="whitespace-nowrap"><x-status-badge :status="$request['status']" /></td>
                            <td class="whitespace-nowrap text-slate-500">{{ $request['date'] }}</td>
                            <td class="whitespace-nowrap text-right">
                                <x-table-action-button :href="url('/repair-requests/1')">View</x-table-action-button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-dashboard-card>
</x-app-layout>
