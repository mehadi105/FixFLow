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

        $response = $next($request);

        $response->headers->set('X-Request-ID', $requestId);
        $response->headers->set('X-FixFlow-App', 'FixFlow');

        return $response;
    }
}
