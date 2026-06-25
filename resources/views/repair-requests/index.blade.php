<x-app-layout :role="$role ?? 'customer'">
    <x-page-header title="Repair Requests" description="View and manage all repair requests">
        @if (auth()->user()->isCustomer())
            <x-slot name="actions">
                <a href="{{ route('repair-requests.create') }}" class="ff-btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    New Request
                </a>
            </x-slot>
        @endif
    </x-page-header>

    <x-dashboard-card>
        <form method="GET" action="{{ route('repair-requests.index') }}" class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center">
            <div class="ff-field flex-1">
                <label for="search" class="sr-only">Search</label>
                <input type="search" id="search" name="search" value="{{ $search }}" placeholder="Search by reference, device, or issue..." class="ff-input">
            </div>
            <div class="ff-field sm:w-48">
                <label for="status" class="sr-only">Filter by status</label>
                <select id="status" name="status" class="ff-input" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    @foreach (\App\Models\RepairRequest::STATUSES as $statusOption)
                        <option value="{{ $statusOption }}" @selected($status === $statusOption)>{{ ucfirst($statusOption) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="ff-btn-secondary">Search</button>
        </form>

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
                    @forelse ($requests as $request)
                        <tr>
                            <td class="cell-id">{{ $request->reference }}</td>
                            <td class="cell-muted">{{ $request->device_type }}</td>
                            <td class="cell-strong">{{ $request->device_label }}</td>
                            <td class="cell-truncate">{{ $request->issue_description }}</td>
                            <td><x-status-badge :status="$request->status" /></td>
                            <td class="cell-muted">{{ $request->technician?->name ?? 'Unassigned' }}</td>
                            <td class="cell-muted">{{ $request->created_at->format('M d, Y') }}</td>
                            <td class="cell-action">
                                <x-table-action-button :href="route('repair-requests.show', $request)">View</x-table-action-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-sm text-slate-500">
                                No repair requests found.
                                @if (auth()->user()->isCustomer())
                                    <a href="{{ route('repair-requests.create') }}" class="font-semibold text-indigo-600 hover:text-indigo-800">Create your first one</a>.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($requests->hasPages())
            <div class="mt-6">
                {{ $requests->links() }}
            </div>
        @endif
    </x-dashboard-card>
</x-app-layout>
