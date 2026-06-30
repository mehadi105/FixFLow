<x-app-layout :role="'customer'">
    <x-hero-banner
        title="Welcome back, {{ auth()->user()->name }}"
        description="Track repair requests, invoices, and warranty coverage from your dashboard."
    >
        <x-slot name="actions">
            <a href="{{ route('repair-requests.create') }}" class="ff-btn-inverse">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                New Repair Request
            </a>
        </x-slot>
    </x-hero-banner>

    <div class="ff-stats-grid">
        <x-stat-card title="Total Requests" :value="$stats['total']" />
        <x-stat-card title="Pending Repairs" :value="$stats['pending']" />
        <x-stat-card title="Completed Repairs" :value="$stats['completed']" />
        <x-stat-card title="Active Warranty" :value="$stats['activeWarranty']" />
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
                    @forelse ($recentRequests as $request)
                        <tr>
                            <td class="cell-id">
                                <span class="inline-flex items-center gap-2">
                                    {{ $request->reference }}
                                    <x-unread-badge :count="$unreadCounts[$request->id] ?? 0" />
                                </span>
                            </td>
                            <td class="cell-strong">{{ $request->device_label }}</td>
                            <td class="cell-truncate">{{ $request->issue_description }}</td>
                            <td><x-status-badge :status="$request->status" /></td>
                            <td class="cell-muted">{{ $request->created_at->format('M d, Y') }}</td>
                            <td class="cell-action">
                                <x-table-action-button :href="route('repair-requests.show', $request)">View</x-table-action-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">
                                You haven't submitted any repair requests yet.
                                <a href="{{ route('repair-requests.create') }}" class="font-semibold text-indigo-600 hover:text-indigo-800">Create one now</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-dashboard-card>
</x-app-layout>
