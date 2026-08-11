<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * DEPLOY-03: Add essential HTTP security headers to every response.
 *
 * CSP is intentionally omitted because the application uses CDN-loaded resources,
 * Alpine.js, inline styles from Tailwind, and dynamic SVG icons.
 * A strict CSP would break the UI and requires a dedicated audit of all
 * scripts/styles before deployment. Add CSP separately after that audit.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent MIME-type sniffing (file upload protection)
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Prevent clickjacking — only allow framing by same origin
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Control referrer information leaked to external sites
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Restrict access to browser features not used by the application
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // HSTS: force HTTPS on subsequent visits — only set on actual HTTPS connections
        // to avoid locking out HTTP-only local development
        if ($request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        return $response;
    }
}
