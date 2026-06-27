<x-app-layout :role="$role ?? 'customer'">
    <x-page-header title="Warranty" description="View warranty coverage for repaired devices">
        @if (auth()->user()->isAdmin())
            <x-slot name="actions">
                <a href="{{ route('warranties.create') }}" class="ff-btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Issue Warranty
                </a>
            </x-slot>
        @endif
    </x-page-header>

    @if (session('status'))
        <div class="mb-6 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700 ring-1 ring-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    @if ($warranties->isEmpty())
        <x-dashboard-card>
            <p class="px-1 py-8 text-center text-sm text-slate-500">No warranties found.</p>
        </x-dashboard-card>
    @else
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:hidden">
            @foreach ($warranties as $warranty)
                <div class="ff-card-flat p-5">
                    <div class="flex items-start justify-between gap-3">
                        <p class="font-mono text-sm font-semibold text-indigo-600">{{ $warranty->warranty_code }}</p>
                        <x-status-badge :status="$warranty->status" />
                    </div>
                    <p class="mt-2 text-sm font-medium text-slate-900">{{ $warranty->repairRequest->device_label }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $warranty->start_date->format('M d, Y') }} — {{ $warranty->end_date->format('M d, Y') }}</p>
                </div>
            @endforeach
        </div>

        <x-dashboard-card title="Warranty Coverage" class="hidden lg:block">
            <div class="ff-table-wrap">
                <table class="ff-table min-w-full">
                    <thead>
                        <tr>
                            <th>Warranty Code</th>
                            @unless (auth()->user()->isCustomer())
                                <th>Customer</th>
                            @endunless
                            <th>Device</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($warranties as $warranty)
                            <tr>
                                <td class="cell-id font-mono">{{ $warranty->warranty_code }}</td>
                                @unless (auth()->user()->isCustomer())
                                    <td class="cell-muted">{{ $warranty->customer->name }}</td>
                                @endunless
                                <td class="cell-strong">{{ $warranty->repairRequest->device_label }}</td>
                                <td class="cell-muted">{{ $warranty->start_date->format('M d, Y') }}</td>
                                <td class="cell-muted">{{ $warranty->end_date->format('M d, Y') }}</td>
                                <td><x-status-badge :status="$warranty->status" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($warranties->hasPages())
                <div class="mt-6">{{ $warranties->links() }}</div>
            @endif
        </x-dashboard-card>
    @endif

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
