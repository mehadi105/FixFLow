<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\RepairRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Business overview report (admins only).
     */
    public function index(Request $request): View
    {
        return view('reports.index', [
            'role' => 'admin',
            'cards' => $this->summaryCards(),
            'revenue' => $this->revenueSummary(),
            'statusSummary' => $this->statusSummary(),
            'monthly' => $this->monthlyRepairs(),
            'technicians' => $this->technicianPerformance(),
        ]);
    }

    /**
     * Top-level KPI cards.
     *
     * @return array<int, array<string, string>>
     */
    private function summaryCards(): array
    {
        $revenue = (float) Invoice::where('payment_status', Invoice::STATUS_PAID)->sum('total');
        $outstanding = (float) Invoice::where('payment_status', Invoice::STATUS_UNPAID)->sum('total');

        return [
            ['title' => 'Total Revenue', 'value' => '$'.number_format($revenue, 2), 'subtitle' => 'Paid invoices'],
            ['title' => 'Repairs Completed', 'value' => (string) RepairRequest::where('status', RepairRequest::STATUS_COMPLETED)->count(), 'subtitle' => 'All time'],
            ['title' => 'Total Requests', 'value' => (string) RepairRequest::count(), 'subtitle' => 'All repair requests'],
            ['title' => 'Outstanding', 'value' => '$'.number_format($outstanding, 2), 'subtitle' => 'Unpaid invoices'],
        ];
    }

    /**
     * Revenue grouped by period (paid invoices only).
     *
     * @return array<string, float>
     */
    private function revenueSummary(): array
    {
        $paid = Invoice::where('payment_status', Invoice::STATUS_PAID);

        return [
            'this_month' => (float) (clone $paid)->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('total'),
            'last_month' => (float) (clone $paid)->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->sum('total'),
            'year_to_date' => (float) (clone $paid)->whereYear('created_at', now()->year)->sum('total'),
        ];
    }

    /**
     * Repair counts grouped by status.
     *
     * @return array<string, int>
     */
    private function statusSummary(): array
    {
        $counts = RepairRequest::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        $result = [];
        foreach (RepairRequest::STATUSES as $status) {
            $result[$status] = $counts[$status] ?? 0;
        }

        return $result;
    }

    /**
     * Repair request counts for the last 6 months.
     *
     * @return array<int, array{label: string, count: int}>
     */
    private function monthlyRepairs(): array
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = [
                'label' => $month->format('M'),
                'count' => RepairRequest::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ];
        }

        return $months;
    }

    /**
     * Per-technician workload and completion rate.
     */
    private function technicianPerformance()
    {
        return User::where('role', User::ROLE_TECHNICIAN)
            ->withCount([
                'assignedRepairRequests as assigned_count',
                'assignedRepairRequests as completed_count' => fn ($q) => $q->where('status', RepairRequest::STATUS_COMPLETED),
            ])
            ->orderByDesc('assigned_count')
            ->get();
    }
}
