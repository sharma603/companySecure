<?php

/**
 * This script creates a LoginController.php file with the correct casing
 * To run: php create-login-controller.php
 */

$controllerPath = __DIR__ . '/app/Http/Controllers/LoginController.php';
$content = <<<'EOT'
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        // If already logged in, redirect to dashboard
        if (Auth::check() || Auth::guard('register')->check()) {
            return redirect()->route('dashboard');
        }
        
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

        // Log the authentication attempt
        Log::info('Login attempt', ['email' => $request->email]);

        // Try to authenticate with Register model first (our default)
        if (Auth::guard('register')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            Log::info('Register login successful', ['email' => $request->email]);
            
            return $request->ajax()
                ? response()->json(['success' => true, 'redirect' => route('dashboard')])
                : redirect()->intended(route('dashboard'));
        }
        
        // If that fails, try with standard User model
        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            Log::info('User login successful', ['email' => $request->email]);
            
            return $request->ajax()
                ? response()->json(['success' => true, 'redirect' => route('dashboard')])
                : redirect()->intended(route('dashboard'));
        }

        // Authentication failed - log the failure
        Log::warning('Authentication failed', ['email' => $request->email]);
        
        // Authentication failed
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

        return redirect()->route('login');
    }
}
EOT;

// Create directories if they don't exist
if (!is_dir(dirname($controllerPath))) {
    mkdir(dirname($controllerPath), 0755, true);
}

// Write the controller file
file_put_contents($controllerPath, $content);

echo "LoginController.php has been created at: " . $controllerPath . "\n";

// Clear Laravel caches
if (file_exists('artisan')) {
    echo "\nClearing Laravel caches...\n";
    system('php artisan optimize:clear');
    system('php artisan config:clear');
    system('php artisan route:clear');
    system('php artisan cache:clear');
    echo "Caches cleared.\n";
}

echo "\nProcess completed.\n"; 