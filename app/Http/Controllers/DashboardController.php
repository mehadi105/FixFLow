<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Message;
use App\Models\RepairRequest;
use App\Models\User;
use App\Models\Warranty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Send the user to the dashboard matching their role.
     */
    public function redirect(Request $request): RedirectResponse
    {
        return redirect()->route($request->user()->dashboardRoute());
    }

    /**
     * Customer dashboard with their own repair stats.
     */
    public function customer(Request $request): View
    {
        $user = $request->user();
        $requests = $user->repairRequests();
        $recentRequests = $user->repairRequests()->latest()->take(5)->get();

        return view('dashboard.customer', [
            'role' => User::ROLE_CUSTOMER,
            'stats' => [
                'total' => (clone $requests)->count(),
                'pending' => (clone $requests)->whereNot('status', RepairRequest::STATUS_COMPLETED)->count(),
                'completed' => (clone $requests)->where('status', RepairRequest::STATUS_COMPLETED)->count(),
                'activeWarranty' => Warranty::where('user_id', $user->id)->whereDate('end_date', '>=', now())->count(),
            ],
            'recentRequests' => $recentRequests,
            'unreadCounts' => Message::unreadCountsByRepairRequestForUser(
                $user,
                $recentRequests->pluck('id')
            ),
        ]);
    }

    /**
     * Admin dashboard with system-wide stats.
     */
    public function admin(Request $request): View
    {
        return view('dashboard.admin', [
            'role' => User::ROLE_ADMIN,
            'stats' => [
                'customers' => User::where('role', User::ROLE_CUSTOMER)->count(),
                'technicians' => User::where('role', User::ROLE_TECHNICIAN)->count(),
                'total' => RepairRequest::count(),
                'pending' => RepairRequest::whereNot('status', RepairRequest::STATUS_COMPLETED)->count(),
                'completed' => RepairRequest::where('status', RepairRequest::STATUS_COMPLETED)->count(),
                'revenue' => (float) Invoice::where('payment_status', Invoice::STATUS_PAID)->sum('total'),
            ],
            'statusCounts' => $this->statusCounts(),
            'monthlyRevenue' => $this->monthlyRevenue(),
            'recentRequests' => RepairRequest::with(['customer', 'technician'])->latest()->take(6)->get(),
        ]);
    }

    /**
     * Technician dashboard with assigned jobs.
     */
    public function technician(Request $request): View
    {
        $user = $request->user();
        $assigned = $user->assignedRepairRequests();
        $jobs = $user->assignedRepairRequests()->with('customer')->latest()->take(8)->get();

        return view('dashboard.technician', [
            'role' => User::ROLE_TECHNICIAN,
            'stats' => [
                'assigned' => (clone $assigned)->count(),
                'inProgress' => (clone $assigned)->whereIn('status', [
                    RepairRequest::STATUS_DIAGNOSING,
                    RepairRequest::STATUS_REPAIRING,
                ])->count(),
                'completed' => (clone $assigned)->where('status', RepairRequest::STATUS_COMPLETED)->count(),
            ],
            'jobs' => $jobs,
            'unreadCounts' => Message::unreadCountsByRepairRequestForUser(
                $user,
                $jobs->pluck('id')
            ),
        ]);
    }

    /**
     * Paid invoice revenue for the last 6 months.
     *
     * @return array<int, array{label: string, amount: float}>
     */
    private function monthlyRevenue(): array
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $months[] = [
                'label' => $month->format('M'),
                'amount' => (float) Invoice::where('payment_status', Invoice::STATUS_PAID)
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('total'),
            ];
        }

        return $months;
    }

    /**
     * Count of repair requests grouped by status.
     *
     * @return array<string, int>
     */
    private function statusCounts(): array
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
}
