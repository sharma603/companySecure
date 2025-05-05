<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Don't redirect AJAX requests
        if ($request->expectsJson() || $request->ajax()) {
            return null;
        }
        
        // FIXED: Add debug logging to track redirect problems
        $currentPath = $request->path();
        Log::debug('Authenticate redirecting unauthenticated user', [
            'from' => $request->fullUrl(),
            'path' => $currentPath,
            'method' => $request->method(),
            'session_id' => $request->session()->getId()
        ]);
        
        // Always use route() instead of hardcoded paths
        return route('login');
    }
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, \Closure $next, ...$guards)
    {
        // If no specific guards provided, check both register and web
        if (empty($guards)) {
            $guards = ['register', 'web'];
        }
        
        // Check if the user is already authenticated with any guard
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                Log::debug('User is authenticated with guard: ' . $guard, [
                    'path' => $request->path(),
                    'session_id' => $request->session()->getId()
                ]);
                return $next($request);
            }
        }
        
        // Not authenticated, proceed with normal authentication process
        return parent::handle($request, $next, ...$guards);
    }
}
