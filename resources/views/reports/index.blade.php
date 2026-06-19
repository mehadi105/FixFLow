@php
    $reportCards = [
        ['title' => 'Total Revenue', 'value' => '$33,300', 'change' => '+12% vs last month'],
        ['title' => 'Repairs Completed', 'value' => '142', 'change' => '+8% vs last month'],
        ['title' => 'Avg. Repair Time', 'value' => '2.4 days', 'change' => '-0.3 days'],
        ['title' => 'Customer Satisfaction', 'value' => '4.7/5', 'change' => 'Based on 89 reviews'],
    ];

    $statusSummary = [
        ['status' => 'Pending', 'count' => 8, 'color' => 'bg-amber-500'],
        ['status' => 'Assigned', 'count' => 6, 'color' => 'bg-blue-500'],
        ['status' => 'Diagnosing', 'count' => 5, 'color' => 'bg-purple-500'],
        ['status' => 'Repairing', 'count' => 10, 'color' => 'bg-indigo-500'],
        ['status' => 'Completed', 'count' => 11, 'color' => 'bg-green-500'],
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

    {{-- Report cards --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($reportCards as $card)
            <x-stat-card :title="$card['title']" :value="$card['value']" :subtitle="$card['change']" />
        @endforeach
    </div>

    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Revenue summary --}}
        <x-dashboard-card title="Revenue Summary">
            <dl class="space-y-4">
                <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                    <dt class="text-sm text-gray-600">This Month</dt>
                    <dd class="text-lg font-semibold text-gray-900">$7,100</dd>
                </div>
                <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                    <dt class="text-sm text-gray-600">Last Month</dt>
                    <dd class="text-lg font-semibold text-gray-900">$5,900</dd>
                </div>
                <div class="flex items-center justify-between rounded-lg bg-indigo-50 px-4 py-3">
                    <dt class="text-sm text-indigo-700">Year to Date</dt>
                    <dd class="text-lg font-semibold text-indigo-700">$33,300</dd>
                </div>
            </dl>
        </x-dashboard-card>

        {{-- Repair status summary --}}
        <x-dashboard-card title="Repair Status Summary">
            <div class="space-y-3">
                @foreach ($statusSummary as $item)
                    <div>
                        <div class="mb-1 flex items-center justify-between text-sm">
                            <span class="text-gray-600">{{ $item['status'] }}</span>
                            <span class="font-medium text-gray-900">{{ $item['count'] }} ({{ round(($item['count'] / $totalRepairs) * 100) }}%)</span>
                        </div>
                        <div class="h-3 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full rounded-full {{ $item['color'] }}" style="width: {{ ($item['count'] / $totalRepairs) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-dashboard-card>
    </div>

    {{-- Monthly repair count placeholder --}}
    <x-dashboard-card title="Monthly Repair Count" description="Placeholder chart — connect to real data later" class="mb-6">
        <div class="flex items-end justify-between gap-3 px-4" style="height: 160px">
            @foreach ($monthlyRepairs as $index => $count)
                <div class="flex flex-1 flex-col items-center gap-2">
                    <span class="text-xs font-medium text-gray-600">{{ $count }}</span>
                    <div class="w-full rounded-t-lg bg-indigo-500 transition-all" style="height: {{ ($count / $maxMonthly) * 120 }}px"></div>
                    <span class="text-xs text-gray-500">{{ $months[$index] }}</span>
                </div>
            @endforeach
        </div>
    </x-dashboard-card>

    {{-- Technician performance --}}
    <x-dashboard-card title="Technician Performance">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Technician</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Assigned</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Completed</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Avg. Time</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Rating</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($technicians as $tech)
                        <tr class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $tech['name'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ $tech['assigned'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ $tech['completed'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ $tech['avg_time'] }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-indigo-600">{{ $tech['rating'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-dashboard-card>
</x-app-layout>
