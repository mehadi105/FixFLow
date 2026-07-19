<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AddFixFlowResponseHeaders
{
    /**
     * Attach a trace ID to both the request and response.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $request->header('X-Request-ID') ?: (string) Str::uuid();

        // Downstream controllers can read the normalized request header.
        $request->headers->set('X-Request-ID', $requestId);

        // Capture auth state before the route runs (logout clears the user).
        $wasAuthenticated = $request->user() !== null;

        $response = $next($request);

        $response->headers->set('X-Request-ID', $requestId);
        $response->headers->set('X-FixFlow-App', 'FixFlow');

        // Stop browsers from keeping private pages in cache / history (back button).
        if ($wasAuthenticated || $request->routeIs('logout', 'login', 'register', 'password.request', 'password.reset', 'password.email', 'password.update')) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        return $response;
    }
}
