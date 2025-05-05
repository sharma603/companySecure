<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class UserLoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Do not apply middleware here - we'll handle authentication checks in each method
    }
    
    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm(Request $request)
    {
        // Direct check - if already logged in with either guard, redirect to dashboard
        if (Auth::guard('register')->check() || Auth::guard('web')->check()) {
            return redirect()->route('dashboard');
        }
        
        // Not logged in, show login form
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // Check if already logged in
        if (Auth::guard('register')->check() || Auth::guard('web')->check()) {
            Log::info('User already logged in, redirecting to dashboard');
            return redirect()->route('dashboard');
        }
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $request->ajax()
                ? response()->json(['success' => false, 'errors' => $validator->errors()])
                : redirect()->back()->withErrors($validator)->withInput($request->except('password'));
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');
        
        Log::info('Login attempt', ['email' => $credentials['email'], 'remember' => $remember]);

        // Try to authenticate with Register model first (our default)
        if (Auth::guard('register')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            Log::info('Login successful with register guard', ['email' => $credentials['email']]);
            $user = Auth::guard('register')->user();
            
            // Add a session variable to indicate the guard used
            session(['auth_guard' => 'register']);
            
            return $request->ajax()
                ? response()->json(['success' => true, 'redirect' => route('dashboard')])
                : redirect()->intended(route('dashboard'));
        }
        
        // If that fails, try with standard User model
        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            Log::info('Login successful with web guard', ['email' => $credentials['email']]);
            $user = Auth::guard('web')->user();
            
            // Add a session variable to indicate the guard used
            session(['auth_guard' => 'web']);
            
            return $request->ajax()
                ? response()->json(['success' => true, 'redirect' => route('dashboard')])
                : redirect()->intended(route('dashboard'));
        }

        // Authentication failed
        Log::warning('Authentication failed', ['email' => $credentials['email']]);
        
        return $request->ajax()
            ? response()->json([
                'success' => false,
                'message' => 'The provided credentials do not match our records.'
            ])
            : redirect()->back()
                ->with('error', 'The provided credentials do not match our records.')
                ->withInput($request->except('password'));
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        // Log out from both guards to be safe
        Auth::guard('web')->logout();
        Auth::guard('register')->logout();
            
        $request->session()->invalidate();
        $request->session()->regenerateToken();
            
        return redirect('/');
    }
}