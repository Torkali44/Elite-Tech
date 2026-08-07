<?php

namespace App\Http\Middleware;

use App\Support\AdminAuth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards every /admin/* route.
 * Accepts two valid states:
 *   1. An authenticated User that isAdmin() → set is_admin flag
 *   2. is_admin session flag + the currently-authenticated user is still admin
 * A session flag alone (without a matching authenticated admin user) is NOT accepted.
 */
class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Case 1: Regular admin user authenticated via Eloquent guard
        if ($user && $user->isAdmin()) {
            $request->session()->put('is_admin', true);
            return $next($request);
        }

        // Case 2: Admin authenticated via AdminAuth (config-based admin email)
        // Verify both the session flag AND that the configured admin is actually logged in
        if ($request->session()->get('is_admin')) {
            $adminUser = AdminAuth::user();
            if ($adminUser && $user && $user->id === $adminUser->id && $user->isAdmin()) {
                return $next($request);
            }

            // Session flag exists but no valid admin user → clear stale flag and redirect
            $request->session()->forget('is_admin');
        }

        return redirect()->route('login')
            ->with('error', 'يرجى تسجيل الدخول بحساب الإدارة للوصول إلى لوحة التحكم.');
    }
}
