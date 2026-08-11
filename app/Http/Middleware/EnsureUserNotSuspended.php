<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserNotSuspended
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        // ── Suspended account: force logout immediately ──────────────────────
        if ($user->is_suspended) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => __('auth.account_suspended')]);
        }

        // ── Unverified email: redirect to verify page ───────────────────────
        // Excludes the verify route itself, logout, and language switcher to
        // prevent redirect loops. All other authenticated routes require
        // a verified email.
        if (
            ! $user->email_verified_at &&
            ! $request->routeIs('auth.verify', 'auth.verify.submit', 'logout', 'lang.switch')
        ) {
            return redirect()->route('auth.verify');
        }

        return $next($request);
    }
}
