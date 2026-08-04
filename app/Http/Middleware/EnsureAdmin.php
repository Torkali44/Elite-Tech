<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards every /admin/* route. Completely independent from the normal
 * user auth guard: the admin never appears in the "users" table and
 * never goes through /login or /register.
 */
class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ($user->role === 'admin' || $user->hasRole('admin'))) {
            $request->session()->put('is_admin', true);
            return $next($request);
        }

        if (! $request->session()->get('is_admin')) {
            return redirect()->route('login')->with('error', 'يرجى تسجيل الدخول بحساب الإدارة للوصول إلى لوحة التحكم.');
        }

        return $next($request);
    }
}
