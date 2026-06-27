<?php

namespace App\Http\Controllers;

use App\Models\RepairRequest;
use App\Models\Warranty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class WarrantyController extends Controller
{
    /**
     * List warranties scoped to the current user's role.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Warranty::query()
            ->with(['customer', 'repairRequest'])
            ->latest();

        // Customers only see their own warranties.
        if ($user->isCustomer()) {
            $query->where('user_id', $user->id);
        }

        return view('warranties.index', [
            'role' => $user->role,
            'warranties' => $query->paginate(12),
        ]);
    }

    /**
     * Show the warranty creation form (admins only).
     */
    public function create(Request $request): View
    {
        return view('warranties.create', [
            'role' => $request->user()->role,
            'repairRequests' => $this->coverableRequests(),
            'selectedRequestId' => $request->integer('repair_request_id') ?: null,
        ]);
    }

    /**
     * Store a new warranty (admins only).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'repair_request_id' => ['required', 'exists:repair_requests,id', 'unique:warranties,repair_request_id'],
            'start_date' => ['required', 'date'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:60'],
        ]);

        $repairRequest = RepairRequest::findOrFail($validated['repair_request_id']);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = $startDate->copy()->addMonths((int) $validated['duration_months']);

        $warranty = Warranty::create([
            'repair_request_id' => $repairRequest->id,
            'user_id' => $repairRequest->user_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $warranty->update(['warranty_code' => 'WRN-'.str_pad((string) $warranty->id, 4, '0', STR_PAD_LEFT)]);

        return redirect()
            ->route('warranties.index')
            ->with('status', 'Warranty issued successfully.');
    }

    /**
     * Repair requests that do not yet have a warranty.
     */
    private function coverableRequests()
    {
        return RepairRequest::query()
            ->with('customer')
            ->whereDoesntHave('warranty')
            ->latest()
            ->get();
    }
}
