<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\VisitorLog;

class VisitorTracker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Don't track static assets or API routes if unnecessary
        if (!$request->is('api/*') && !$request->ajax() && $request->method() === 'GET') {
            try {
                VisitorLog::create([
                    'user_id' => auth()->id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => substr($request->userAgent(), 0, 255),
                    'method' => $request->method(),
                    'url' => substr($request->fullUrl(), 0, 255),
                ]);
            } catch (\Exception $e) {
                // Ignore DB errors during logging to not break the app
            }
        }

        return $next($request);
    }
}
