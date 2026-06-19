@php
    $warranties = [
        ['code' => 'WRN-8847-APL', 'device' => 'Apple iPhone 14 Pro', 'start' => 'Jun 18, 2026', 'end' => 'Dec 18, 2026', 'status' => 'Active'],
        ['code' => 'WRN-7721-SAM', 'device' => 'Samsung Galaxy S23', 'start' => 'May 10, 2026', 'end' => 'Nov 10, 2026', 'status' => 'Active'],
        ['code' => 'WRN-6614-DEL', 'device' => 'Dell XPS 15', 'start' => 'Jan 15, 2025', 'end' => 'Jan 15, 2026', 'status' => 'Expired'],
        ['code' => 'WRN-5502-APL', 'device' => 'Apple iPad Air', 'start' => 'Mar 01, 2026', 'end' => 'Sep 01, 2026', 'status' => 'Active'],
    ];
@endphp

<x-app-layout :role="$role ?? 'customer'">
    <x-page-header title="Warranty" description="View warranty coverage for your repaired devices" />

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:hidden">
        @foreach ($warranties as $warranty)
            <div class="ff-card-flat p-5">
                <div class="flex items-start justify-between gap-3">
                    <p class="font-mono text-sm font-semibold text-indigo-600">{{ $warranty['code'] }}</p>
                    <x-status-badge :status="$warranty['status']" />
                </div>
                <p class="mt-2 text-sm font-medium text-slate-900">{{ $warranty['device'] }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $warranty['start'] }} — {{ $warranty['end'] }}</p>
            </div>
        @endforeach
    </div>

    <x-dashboard-card title="Warranty Coverage" class="hidden lg:block">
        <div class="ff-table-wrap">
            <table class="ff-table min-w-full">
                <thead>
                    <tr>
                        <th>Warranty Code</th>
                        <th>Device</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($warranties as $warranty)
                        <tr>
                            <td class="cell-id font-mono">{{ $warranty['code'] }}</td>
                            <td class="cell-strong">{{ $warranty['device'] }}</td>
                            <td class="cell-muted">{{ $warranty['start'] }}</td>
                            <td class="cell-muted">{{ $warranty['end'] }}</td>
                            <td><x-status-badge :status="$warranty['status']" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-dashboard-card>

    <x-dashboard-card title="Warranty Terms" class="mt-6">
        <ul class="list-disc space-y-2 pl-5 text-sm text-slate-600">
            <li>Standard warranty covers parts and labor for repairs performed by FixFlow technicians.</li>
            <li>Warranty period is 6 months from the date of repair completion.</li>
            <li>Warranty does not cover accidental damage, water damage, or unauthorized modifications.</li>
            <li>To claim warranty service, present your warranty code and original repair invoice.</li>
            <li>FixFlow reserves the right to repair or replace the device at its discretion.</li>
        </ul>
    </x-dashboard-card>
</x-app-layout>
