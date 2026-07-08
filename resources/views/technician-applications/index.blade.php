<x-app-layout :role="$role ?? 'admin'">
    <x-page-header title="Technician Applications" description="Review and approve technician applicants before they can take jobs" />

    @if (session('status'))
        <div class="mb-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700 ring-1 ring-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    <div class="ff-stats-grid mb-6">
        <x-stat-card title="Pending" :value="$counts['pending']" />
        <x-stat-card title="Approved" :value="$counts['approved']" />
        <x-stat-card title="Rejected" :value="$counts['rejected']" />
    </div>

    <x-dashboard-card>
        <form method="GET" action="{{ route('technician-applications.index') }}" class="mb-6">
            <div class="ff-field sm:w-56">
                <label for="status" class="sr-only">Filter by status</label>
                <select id="status" name="status" class="ff-input" onchange="this.form.submit()">
                    <option value="">All statuses</option>
                    <option value="pending" @selected($statusFilter === 'pending')>Pending</option>
                    <option value="approved" @selected($statusFilter === 'approved')>Approved</option>
                    <option value="rejected" @selected($statusFilter === 'rejected')>Rejected</option>
                </select>
            </div>
        </form>

        <div class="ff-table-wrap">
            <table class="ff-table min-w-full">
                <thead>
                    <tr>
                        <th>Applicant</th>
                        <th>Experience</th>
                        <th>Specialties</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($applications as $application)
                        <tr>
                            <td class="cell-strong">
                                {{ $application->applicant->name }}
                                <p class="text-xs font-normal text-slate-500">{{ $application->applicant->email }}</p>
                            </td>
                            <td class="cell-muted">{{ $application->years_experience }} yrs</td>
                            <td class="cell-truncate">{{ $application->specialties }}</td>
                            <td><x-status-badge :status="$application->status" /></td>
                            <td class="cell-muted">{{ $application->created_at->format('M d, Y') }}</td>
                            <td class="cell-action">
                                <x-table-action-button :href="route('technician-applications.show', $application)">Review</x-table-action-button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">No applications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($applications->hasPages())
            <div class="mt-6">{{ $applications->links() }}</div>
        @endif
    </x-dashboard-card>
</x-app-layout>
