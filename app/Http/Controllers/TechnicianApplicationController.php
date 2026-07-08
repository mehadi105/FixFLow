<?php

namespace App\Http\Controllers;

use App\Models\TechnicianApplication;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class TechnicianApplicationController extends Controller
{
    /**
     * Show the technician application form (guests).
     */
    public function create(): View
    {
        return view('auth.technician-apply');
    }

    /**
     * Submit a new technician application and account.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:30'],
            'years_experience' => ['required', 'integer', 'min:0', 'max:40'],
            'specialties' => ['required', 'string', 'max:255'],
            'certification' => ['nullable', 'string', 'max:255'],
            'motivation' => ['required', 'string', 'max:2000'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => User::ROLE_TECHNICIAN,
                'password' => Hash::make($validated['password']),
            ]);

            TechnicianApplication::create([
                'user_id' => $user->id,
                'phone' => $validated['phone'],
                'years_experience' => $validated['years_experience'],
                'specialties' => $validated['specialties'],
                'certification' => $validated['certification'] ?? null,
                'motivation' => $validated['motivation'],
                'status' => TechnicianApplication::STATUS_PENDING,
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('technician.application.status')
            ->with('status', 'Your application was submitted. An admin will review it soon.');
    }

    /**
     * Application status for the logged-in technician applicant.
     */
    public function status(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user->isTechnician()) {
            abort(403);
        }

        if ($user->isApprovedTechnician()) {
            return redirect()->route('dashboard.technician');
        }

        $application = $user->technicianApplication;

        if (! $application) {
            abort(404);
        }

        return view('technician.application-status', [
            'role' => $user->role,
            'application' => $application,
        ]);
    }

    /**
     * List technician applications (admins only).
     */
    public function index(Request $request): View
    {
        $query = TechnicianApplication::query()
            ->with(['applicant', 'reviewer'])
            ->latest();

        if ($status = $request->string('status')->trim()->value()) {
            $query->where('status', $status);
        }

        return view('technician-applications.index', [
            'role' => $request->user()->role,
            'applications' => $query->paginate(12)->withQueryString(),
            'statusFilter' => $status ?? '',
            'counts' => [
                'pending' => TechnicianApplication::where('status', TechnicianApplication::STATUS_PENDING)->count(),
                'approved' => TechnicianApplication::where('status', TechnicianApplication::STATUS_APPROVED)->count(),
                'rejected' => TechnicianApplication::where('status', TechnicianApplication::STATUS_REJECTED)->count(),
            ],
        ]);
    }

    /**
     * Review a single technician application.
     */
    public function show(Request $request, TechnicianApplication $technicianApplication): View
    {
        $technicianApplication->load(['applicant', 'reviewer']);

        return view('technician-applications.show', [
            'role' => $request->user()->role,
            'application' => $technicianApplication,
        ]);
    }

    /**
     * Approve a pending application.
     */
    public function approve(Request $request, TechnicianApplication $technicianApplication): RedirectResponse
    {
        if (! $technicianApplication->isPending()) {
            return back()->withErrors(['application' => 'This application has already been reviewed.']);
        }

        $technicianApplication->update([
            'status' => TechnicianApplication::STATUS_APPROVED,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'admin_notes' => $request->string('admin_notes')->trim()->value() ?: null,
        ]);

        $technicianApplication->load('applicant');

        return redirect()
            ->route('technician-applications.index')
            ->with('status', $technicianApplication->applicant->name.' is approved and can now work as a technician.');
    }

    /**
     * Reject a pending application.
     */
    public function reject(Request $request, TechnicianApplication $technicianApplication): RedirectResponse
    {
        $validated = $request->validate([
            'admin_notes' => ['required', 'string', 'max:1000'],
        ]);

        if (! $technicianApplication->isPending()) {
            return back()->withErrors(['application' => 'This application has already been reviewed.']);
        }

        $technicianApplication->update([
            'status' => TechnicianApplication::STATUS_REJECTED,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'admin_notes' => $validated['admin_notes'],
        ]);

        return redirect()
            ->route('technician-applications.index')
            ->with('status', 'Application rejected. The applicant has been notified in their portal.');
    }
}
