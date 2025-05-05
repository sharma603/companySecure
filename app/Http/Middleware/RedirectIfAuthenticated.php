<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        // Don't redirect AJAX requests
        if ($request->expectsJson() || $request->ajax()) {
            return $next($request);
        }
        
        // Default guards to check
        $guards = empty($guards) ? ['register', 'web'] : $guards;
        
        // Get current URL path
        $currentPath = $request->path();
        
        // Log the request for debugging
        Log::debug('RedirectIfAuthenticated checking path', [
            'path' => $currentPath,
            'method' => $request->method(),
            'user_agent' => $request->userAgent()
        ]);
        
        // CRITICAL FIX: Never redirect when already on dashboard
        if ($currentPath === 'dashboard' || $request->is('dashboard') || $request->route() && $request->route()->getName() === 'dashboard') {
            return $next($request);
        }
        
        // Only redirect from explicitly public pages (login, register)
        $publicRoutes = ['login', 'register'];
        $isPublicRoute = in_array($currentPath, $publicRoutes) || 
                        $request->is($publicRoutes) ||
                        $currentPath === '/';
        
        if ($isPublicRoute) {
            foreach ($guards as $guard) {
                if (Auth::guard($guard)->check()) {
                    Log::debug('Authenticated user trying to access public route - redirecting to dashboard', [
                        'guard' => $guard,
                        'from_path' => $currentPath
                    ]);
                    return redirect(RouteServiceProvider::HOME);
                }
            }
        }

        return $next($request);
    }
}
