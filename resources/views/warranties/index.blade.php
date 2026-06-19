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

    {{-- Warranty cards (mobile-friendly) --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:hidden">
        @foreach ($warranties as $warranty)
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-900/5">
                <div class="flex items-start justify-between">
                    <p class="font-mono text-sm font-semibold text-indigo-600">{{ $warranty['code'] }}</p>
                    <x-status-badge :status="$warranty['status']" />
                </div>
                <p class="mt-2 text-sm font-medium text-gray-900">{{ $warranty['device'] }}</p>
                <p class="mt-1 text-xs text-gray-500">{{ $warranty['start'] }} — {{ $warranty['end'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Warranty table (desktop) --}}
    <x-dashboard-card title="Warranty Coverage" class="hidden lg:block">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Warranty Code</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Device</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Start Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">End Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($warranties as $warranty)
                        <tr class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-4 py-3 font-mono text-sm font-medium text-indigo-600">{{ $warranty['code'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900">{{ $warranty['device'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $warranty['start'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $warranty['end'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3"><x-status-badge :status="$warranty['status']" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-dashboard-card>

    {{-- Terms section --}}
    <x-dashboard-card title="Warranty Terms" class="mt-6">
        <div class="prose prose-sm max-w-none text-gray-600">
            <ul class="list-disc space-y-2 pl-5 text-sm">
                <li>Standard warranty covers parts and labor for repairs performed by FixFlow technicians.</li>
                <li>Warranty period is 6 months from the date of repair completion.</li>
                <li>Warranty does not cover accidental damage, water damage, or unauthorized modifications.</li>
                <li>To claim warranty service, present your warranty code and original repair invoice.</li>
                <li>FixFlow reserves the right to repair or replace the device at its discretion.</li>
            </ul>
        </div>
    </x-dashboard-card>
</x-app-layout>
