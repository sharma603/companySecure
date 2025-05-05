# Fixing Redirect Loops in Laravel

If you're seeing the error "redirected you too many times" (ERR_TOO_MANY_REDIRECTS), this guide will help you diagnose and fix the problem.

## Quick Fixes

Try these immediate solutions first:

1. **Clear your browser cookies and cache**
2. **Clear Laravel cache**: Run `php artisan config:clear` and `php artisan cache:clear`
3. **Check your application in Incognito/Private browsing mode**

## Step-by-Step Diagnosis

### 1. Identify the Redirecting Route

Run the application with debug enabled (`APP_DEBUG=true` in .env) and check:

- Which route is causing the loop
- What middleware is attached to that route
- Whether the loop happens only when logged in/out

### 2. Check Authentication Middleware

The two primary files to check are:

**a) `app/Http/Middleware/Authenticate.php`**

Replace hardcoded redirect paths with named routes:

```php
// CHANGE THIS:
return '/login';

// TO THIS: 
return route('login');
```

**b) `app/Http/Middleware/RedirectIfAuthenticated.php`**

Make sure this middleware has proper checks to avoid loops:

```php
// RECOMMENDED APPROACH:
public function handle(Request $request, Closure $next, string ...$guards): Response
{
    // Don't redirect AJAX requests
    if ($request->expectsJson() || $request->ajax()) {
        return $next($request);
    }
    
    // Default guards to check
    $guards = empty($guards) ? ['register', 'web'] : $guards;
    
    // Only redirect from explicitly public pages (login, register)
    $publicRoutes = ['login', 'register'];
    $isPublicRoute = in_array($request->path(), $publicRoutes) || 
                    $request->is($publicRoutes) ||
                    $request->path() === '/';
    
    if ($isPublicRoute) {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect(RouteServiceProvider::HOME);
            }
        }
    }

    return $next($request);
}
```

### 3. Check Route Definitions

In `routes/web.php`:

1. Use named routes instead of hard URLs:
   ```php
   // CHANGE THIS:
   return redirect('/dashboard');
   
   // TO THIS:
   return redirect()->route('dashboard');
   ```

2. Watch for circular redirects:
   ```php
   // PROBLEMATIC:
   Route::get('/', function () {
       return redirect('/home');
   });
   
   Route::get('/home', function () {
       // Some condition might redirect back to '/'
       if (some_condition()) return redirect('/');
   });
   ```

3. Avoid multiple redirects for the same URL pattern with trailing/non-trailing slashes:
   ```php
   // AVOID this pattern:
   Route::get('/login', function () { /* ... */ });
   Route::get('/login/', function () { return redirect('/login'); });
   ```

### 4. Check for HTTPS Redirects

If your application forces HTTPS but you're testing locally:

1. Check your `.htaccess` file for forced HTTPS redirects
2. Modify your AppServiceProvider to only use HTTPS in production:
   ```php
   if ($this->app->environment('production')) {
       URL::forceScheme('https');
   }
   ```

### 5. Session Configuration

1. Make sure your `config/session.php` settings are correct
2. Verify the `APP_URL` in your `.env` file matches the domain you're using

## Solution for Hostinger

When deploying to Hostinger, make sure:

1. Your `.env` file has the correct `APP_URL` set to your Hostinger domain
2. Session and cookie configuration are properly set for your domain
3. You're using named routes consistently throughout your application
4. You've cleared all caches after deployment:
   ```
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

After making changes, clear your browser cookies and try accessing your application again. 