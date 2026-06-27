@php
    $totalRepairs = array_sum($statusSummary);
    $maxMonthly = max(array_map(fn ($m) => $m['count'], $monthly)) ?: 1;
@endphp

<x-app-layout :role="'admin'">
    <x-page-header title="Reports" description="Business overview and performance analytics" />

    <div class="ff-stats-grid">
        @foreach ($cards as $card)
            <x-stat-card :title="$card['title']" :value="$card['value']" :subtitle="$card['subtitle']" />
        @endforeach
    </div>

    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <x-dashboard-card title="Revenue Summary">
            <dl class="space-y-3">
                <div class="ff-kpi-box">
                    <dt class="text-sm text-slate-600">This Month</dt>
                    <dd class="text-lg font-semibold text-slate-900">${{ number_format($revenue['this_month'], 2) }}</dd>
                </div>
                <div class="ff-kpi-box">
                    <dt class="text-sm text-slate-600">Last Month</dt>
                    <dd class="text-lg font-semibold text-slate-900">${{ number_format($revenue['last_month'], 2) }}</dd>
                </div>
                <div class="ff-kpi-box-highlight">
                    <dt class="text-sm text-indigo-700">Year to Date</dt>
                    <dd class="text-lg font-semibold text-indigo-700">${{ number_format($revenue['year_to_date'], 2) }}</dd>
                </div>
            </dl>
        </x-dashboard-card>

        <x-dashboard-card title="Repair Status Summary">
            @if ($totalRepairs === 0)
                <p class="py-6 text-center text-sm text-slate-500">No repair data yet.</p>
            @else
                <div class="space-y-4">
                    @foreach ($statusSummary as $status => $count)
                        @php $percent = round(($count / $totalRepairs) * 100); @endphp
                        <x-progress-bar
                            :label="ucfirst($status)"
                            :value="$count . ' (' . $percent . '%)'"
                            :percent="$percent"
                        />
                    @endforeach
                </div>
            @endif
        </x-dashboard-card>
    </div>

    <x-dashboard-card title="Monthly Repair Count" description="Repair requests created over the last 6 months" class="mb-6">
        <div class="flex h-40 items-end justify-between gap-3 px-2 sm:px-4">
            @foreach ($monthly as $item)
                <div class="flex flex-1 flex-col items-center gap-2">
                    <span class="text-xs font-medium text-slate-600">{{ $item['count'] }}</span>
                    <div class="w-full rounded-t-lg ff-progress-bar" style="height: {{ ($item['count'] / $maxMonthly) * 120 }}px"></div>
                    <span class="text-xs text-slate-500">{{ $item['label'] }}</span>
                </div>
            @endforeach
        </div>
    </x-dashboard-card>

    <x-dashboard-card title="Technician Performance">
        <div class="ff-table-wrap">
            <table class="ff-table min-w-full">
                <thead>
                    <tr>
                        <th>Technician</th>
                        <th>Assigned</th>
                        <th>Completed</th>
                        <th>Completion Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($technicians as $tech)
                        @php $rate = $tech->assigned_count > 0 ? round(($tech->completed_count / $tech->assigned_count) * 100) : 0; @endphp
                        <tr>
                            <td class="cell-strong">{{ $tech->name }}</td>
                            <td class="cell-muted">{{ $tech->assigned_count }}</td>
                            <td class="cell-muted">{{ $tech->completed_count }}</td>
                            <td class="font-semibold text-indigo-600">{{ $rate }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-sm text-slate-500">No technicians yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-dashboard-card>
</x-app-layout>
