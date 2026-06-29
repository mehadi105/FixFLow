<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * List all users with optional role filtering (admins only).
     */
    public function index(Request $request): View
    {
        $query = User::query()->withCount('repairRequests')->latest();

        if ($role = $request->string('role')->trim()->value()) {
            $query->where('role', $role);
        }

        return view('users.index', [
            'role' => $request->user()->role,
            'users' => $query->paginate(15)->withQueryString(),
            'roleFilter' => $role ?? '',
            'counts' => [
                'all' => User::count(),
                'customer' => User::where('role', User::ROLE_CUSTOMER)->count(),
                'technician' => User::where('role', User::ROLE_TECHNICIAN)->count(),
                'admin' => User::where('role', User::ROLE_ADMIN)->count(),
            ],
        ]);
    }

    /**
     * Update a user's role (admins only).
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        // Prevent an admin from changing their own role and losing access.
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['role' => 'You cannot change your own role.']);
        }

        $validated = $request->validate([
            'role' => ['required', Rule::in([User::ROLE_CUSTOMER, User::ROLE_TECHNICIAN, User::ROLE_ADMIN])],
        ]);

        $user->update(['role' => $validated['role']]);

        return back()->with('status', "{$user->name}'s role updated to {$validated['role']}.");
    }
}
