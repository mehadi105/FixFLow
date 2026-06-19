@php
    $reportCards = [
        ['title' => 'Total Revenue', 'value' => '$33,300', 'change' => '+12% vs last month'],
        ['title' => 'Repairs Completed', 'value' => '142', 'change' => '+8% vs last month'],
        ['title' => 'Avg. Repair Time', 'value' => '2.4 days', 'change' => '-0.3 days'],
        ['title' => 'Customer Satisfaction', 'value' => '4.7/5', 'change' => 'Based on 89 reviews'],
    ];

    $statusSummary = [
        ['status' => 'Pending', 'count' => 8],
        ['status' => 'Assigned', 'count' => 6],
        ['status' => 'Diagnosing', 'count' => 5],
        ['status' => 'Repairing', 'count' => 10],
        ['status' => 'Completed', 'count' => 11],
    ];
    $totalRepairs = array_sum(array_column($statusSummary, 'count'));

    $technicians = [
        ['name' => 'Mike Torres', 'assigned' => 12, 'completed' => 10, 'avg_time' => '2.1 days', 'rating' => '4.8'],
        ['name' => 'Lisa Chen', 'assigned' => 10, 'completed' => 9, 'avg_time' => '2.5 days', 'rating' => '4.6'],
        ['name' => 'David Park', 'assigned' => 8, 'completed' => 7, 'avg_time' => '2.8 days', 'rating' => '4.5'],
        ['name' => 'Emma Johnson', 'assigned' => 6, 'completed' => 6, 'avg_time' => '2.0 days', 'rating' => '4.9'],
    ];

    $monthlyRepairs = [18, 22, 19, 25, 24, 28];
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
    $maxMonthly = max($monthlyRepairs);
@endphp

<x-app-layout :role="'admin'">
    <x-page-header title="Reports" description="Business overview and performance analytics" />

    <div class="ff-stats-grid">
        @foreach ($reportCards as $card)
            <x-stat-card :title="$card['title']" :value="$card['value']" :subtitle="$card['change']" />
        @endforeach
    </div>

    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <x-dashboard-card title="Revenue Summary">
            <dl class="space-y-3">
                <div class="ff-kpi-box">
                    <dt class="text-sm text-slate-600">This Month</dt>
                    <dd class="text-lg font-semibold text-slate-900">$7,100</dd>
                </div>
                <div class="ff-kpi-box">
                    <dt class="text-sm text-slate-600">Last Month</dt>
                    <dd class="text-lg font-semibold text-slate-900">$5,900</dd>
                </div>
                <div class="ff-kpi-box-highlight">
                    <dt class="text-sm text-indigo-700">Year to Date</dt>
                    <dd class="text-lg font-semibold text-indigo-700">$33,300</dd>
                </div>
            </dl>
        </x-dashboard-card>

        <x-dashboard-card title="Repair Status Summary">
            <div class="space-y-4">
                @foreach ($statusSummary as $item)
                    @php $percent = round(($item['count'] / $totalRepairs) * 100); @endphp
                    <x-progress-bar
                        :label="$item['status']"
                        :value="$item['count'] . ' (' . $percent . '%)'"
                        :percent="$percent"
                    />
                @endforeach
            </div>
        </x-dashboard-card>
    </div>

    <x-dashboard-card title="Monthly Repair Count" description="Placeholder chart — connect to real data later" class="mb-6">
        <div class="flex h-40 items-end justify-between gap-3 px-2 sm:px-4">
            @foreach ($monthlyRepairs as $index => $count)
                <div class="flex flex-1 flex-col items-center gap-2">
                    <span class="text-xs font-medium text-slate-600">{{ $count }}</span>
                    <div class="w-full rounded-t-lg ff-progress-bar" style="height: {{ ($count / $maxMonthly) * 120 }}px"></div>
                    <span class="text-xs text-slate-500">{{ $months[$index] }}</span>
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
                        <th>Avg. Time</th>
                        <th>Rating</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($technicians as $tech)
                        <tr>
                            <td class="cell-strong">{{ $tech['name'] }}</td>
                            <td class="cell-muted">{{ $tech['assigned'] }}</td>
                            <td class="cell-muted">{{ $tech['completed'] }}</td>
                            <td class="cell-muted">{{ $tech['avg_time'] }}</td>
                            <td class="font-semibold text-indigo-600">{{ $tech['rating'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-dashboard-card>
</x-app-layout>
