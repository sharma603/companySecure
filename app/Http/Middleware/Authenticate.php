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
        if ($request->expectsJson() || $request->ajax()) {
            return null;
        }
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
                // Ensure the resolved default guard matches the authenticated one
                // so Auth::user() works consistently in controllers/views.
                Auth::shouldUse($guard);
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
