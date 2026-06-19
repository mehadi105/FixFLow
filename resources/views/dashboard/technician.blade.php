@php
    $assignedJobs = [
        ['id' => 'JOB-2041', 'customer' => 'Sarah Ahmed', 'device' => 'iPhone 14 Pro', 'issue' => 'Cracked screen replacement', 'status' => 'Repairing', 'priority' => 'High'],
        ['id' => 'JOB-2038', 'customer' => 'James Wilson', 'device' => 'MacBook Pro 16"', 'issue' => 'Logic board diagnosis', 'status' => 'Diagnosing', 'priority' => 'Medium'],
        ['id' => 'JOB-2035', 'customer' => 'Robert Kim', 'device' => 'HP Pavilion', 'issue' => 'HDD replacement', 'status' => 'Assigned', 'priority' => 'Low'],
        ['id' => 'JOB-2032', 'customer' => 'Lisa Park', 'device' => 'Google Pixel 8', 'issue' => 'Charging port repair', 'status' => 'Waiting for Parts', 'priority' => 'Medium'],
    ];

    $todaysTasks = [
        ['time' => '09:00 AM', 'task' => 'Diagnose MacBook Pro — JOB-2038', 'done' => true],
        ['time' => '11:30 AM', 'task' => 'Replace iPhone screen — JOB-2041', 'done' => false],
        ['time' => '02:00 PM', 'task' => 'Order parts for Pixel 8 — JOB-2032', 'done' => false],
        ['time' => '04:30 PM', 'task' => 'Update repair status for HP Pavilion — JOB-2035', 'done' => false],
    ];
@endphp

<x-app-layout :role="'technician'">
    {{-- Stats cards --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card title="Assigned Jobs" value="7" />
        <x-stat-card title="In Progress" value="3" />
        <x-stat-card title="Completed Today" value="2" />
        <x-stat-card title="Waiting for Parts" value="1" />
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Assigned jobs table --}}
        <div class="lg:col-span-2">
            <x-dashboard-card title="Assigned Repair Jobs" description="Jobs currently assigned to you">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Job ID</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Customer</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Device</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Issue</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Priority</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($assignedJobs as $job)
                                <tr class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-indigo-600">{{ $job['id'] }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900">{{ $job['customer'] }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ $job['device'] }}</td>
                                    <td class="max-w-xs truncate px-4 py-3 text-sm text-gray-600">{{ $job['issue'] }}</td>
                                    <td class="whitespace-nowrap px-4 py-3"><x-status-badge :status="$job['status']" /></td>
                                    <td class="whitespace-nowrap px-4 py-3"><x-status-badge :status="$job['priority']" /></td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right">
                                        <x-table-action-button :href="url('/repair-requests/1')">View</x-table-action-button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex flex-wrap gap-3 border-t border-gray-100 pt-4">
                    <a href="{{ url('/repair-requests/1') }}" class="ff-btn-primary">View Job</a>
                    <a href="#" class="ff-btn-secondary">Add Diagnosis</a>
                    <a href="#" class="ff-btn-secondary">Update Status</a>
                </div>
            </x-dashboard-card>
        </div>

        {{-- Today's Tasks --}}
        <div>
            <x-dashboard-card title="Today's Tasks" description="Your schedule for today">
                <ul class="space-y-4">
                    @foreach ($todaysTasks as $task)
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full {{ $task['done'] ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                                @if ($task['done'])
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                @else
                                    <span class="h-2 w-2 rounded-full bg-gray-300"></span>
                                @endif
                            </span>
                            <div>
                                <p class="text-xs font-medium text-indigo-600">{{ $task['time'] }}</p>
                                <p class="text-sm text-gray-700 {{ $task['done'] ? 'line-through text-gray-400' : '' }}">{{ $task['task'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </x-dashboard-card>
        </div>
    </div>
</x-app-layout>
