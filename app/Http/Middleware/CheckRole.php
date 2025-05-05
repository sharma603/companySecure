<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use BadMethodCallException;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect('/login');
        }

        // If this is a request to dashboard, always allow it
        if ($request->route()->getName() === 'dashboard' || $request->is('dashboard')) {
            return $next($request);
        }

        try {
            // Check if user has any of the specified roles
            foreach ($roles as $role) {
                if (method_exists($request->user(), 'hasRole') && $request->user()->hasRole($role)) {
                    return $next($request);
                }
            }
            
            // No role matches, redirect to dashboard with error
            return redirect('/dashboard')->with('error', 'You do not have permission to access this page.');
        } catch (BadMethodCallException $e) {
            // Default to admin check (fallback)
            if (in_array('admin', $roles) && $request->user()->email === env('ADMIN_EMAIL', 'admin@example.com')) {
                return $next($request);
            }
            
            // No admin match either, redirect to dashboard with error
            return redirect('/dashboard')->with('error', 'You do not have permission to access this page.');
        }
    }
} 