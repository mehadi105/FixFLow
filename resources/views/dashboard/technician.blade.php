@php
    $todaysTasks = [
        ['time' => '09:00 AM', 'task' => 'Diagnose MacBook Pro — JOB-2038', 'done' => true],
        ['time' => '11:30 AM', 'task' => 'Replace iPhone screen — JOB-2041', 'done' => false],
        ['time' => '02:00 PM', 'task' => 'Order parts for Pixel 8 — JOB-2032', 'done' => false],
        ['time' => '04:30 PM', 'task' => 'Update repair status for HP Pavilion — JOB-2035', 'done' => false],
    ];
@endphp

<x-app-layout :role="'technician'">
    <x-page-header title="Technician Dashboard" description="Your assigned jobs and daily tasks" />

    <div class="ff-stats-grid">
        <x-stat-card title="Assigned Jobs" :value="$stats['assigned']" />
        <x-stat-card title="In Progress" :value="$stats['inProgress']" />
        <x-stat-card title="Completed" :value="$stats['completed']" />
        <x-stat-card title="Waiting for Parts" value="0" />
    </div>

    <div class="ff-grid-sidebar">
        <div class="lg:col-span-2">
            <x-dashboard-card title="Assigned Repair Jobs" description="Jobs currently assigned to you">
                <div class="ff-table-wrap">
                    <table class="ff-table min-w-full">
                        <thead>
                            <tr>
                                <th>Job ID</th>
                                <th>Customer</th>
                                <th>Device</th>
                                <th>Issue</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($jobs as $job)
                                <tr>
                                    <td class="cell-id">{{ $job->reference }}</td>
                                    <td class="cell-strong">{{ $job->customer->name }}</td>
                                    <td class="cell-muted">{{ $job->device_label }}</td>
                                    <td class="cell-truncate">{{ $job->issue_description }}</td>
                                    <td><x-status-badge :status="$job->status" /></td>
                                    <td><x-status-badge :status="$job->priority" /></td>
                                    <td class="cell-action">
                                        <x-table-action-button :href="route('repair-requests.show', $job)">View</x-table-action-button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">No jobs assigned to you yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="ff-card-actions-inline">
                    <a href="{{ route('repair-requests.index') }}" class="ff-btn-primary">View All Jobs</a>
                </div>
            </x-dashboard-card>
        </div>

        <x-dashboard-card title="Today's Tasks" description="Your schedule for today">
            <ul class="space-y-4">
                @foreach ($todaysTasks as $task)
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full {{ $task['done'] ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                            @if ($task['done'])
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                            @else
                                <span class="h-2 w-2 rounded-full bg-slate-300"></span>
                            @endif
                        </span>
                        <div>
                            <p class="text-xs font-medium text-indigo-600">{{ $task['time'] }}</p>
                            <p class="text-sm {{ $task['done'] ? 'text-slate-400 line-through' : 'text-slate-700' }}">{{ $task['task'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </x-dashboard-card>
    </div>
</x-app-layout>
