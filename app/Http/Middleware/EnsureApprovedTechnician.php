<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApprovedTechnician
{
    /**
     * Block technicians who have not been approved by an admin yet.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->isTechnician() && ! $user->isApprovedTechnician()) {
            if (! $request->routeIs('technician.application.*')) {
                return redirect()->route('technician.application.status');
            }
        }

        return $next($request);
    }
}
