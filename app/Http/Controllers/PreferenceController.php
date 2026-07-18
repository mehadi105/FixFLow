<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\Rule;

class PreferenceController extends Controller
{
    /**
     * Persist a non-sensitive table-density preference in a cookie.
     */
    public function updateTableDensity(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'density' => ['required', Rule::in(['comfortable', 'compact'])],
        ]);

        Cookie::queue(Cookie::make(
            name: 'ff_table_density',
            value: $validated['density'],
            minutes: 60 * 24 * 365,
            path: '/',
            secure: $request->isSecure(),
            httpOnly: true,
            sameSite: 'lax',
        ));

        return back()->with('status', 'Display preference saved.');
    }
}
