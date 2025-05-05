<?php
/**
 * Login Debug Script
 * Place this file in your Laravel project root and access it directly via your browser.
 * This script will help identify session, cookie, and authentication issues.
 */

// Ensure this is only run in a web context
if (php_sapi_name() === 'cli') {
    die('This script must be run in a web browser');
}

// Basic info
echo "<h1>Login System Diagnostic Tool</h1>";

// Session info
echo "<h2>Session Information</h2>";
echo "<pre>";
session_start();
echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive') . "\n";
echo "Session ID: " . session_id() . "\n";
echo "Session Name: " . session_name() . "\n";
echo "Session Save Path: " . session_save_path() . "\n";
echo "Session Cookie Parameters: ";
print_r(session_get_cookie_params());
echo "</pre>";

// Cookie info
echo "<h2>Cookie Information</h2>";
echo "<pre>";
echo "Cookies set on this domain:\n";
if (!empty($_COOKIE)) {
    foreach ($_COOKIE as $name => $value) {
        echo "- $name: " . (is_array($value) ? 'Array Value' : substr($value, 0, 30) . (strlen($value) > 30 ? '...' : '')) . "\n";
    }
} else {
    echo "No cookies found\n";
}
echo "</pre>";

// Environment
echo "<h2>Environment Information</h2>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "HTTP Host: " . $_SERVER['HTTP_HOST'] . "\n";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "HTTPS: " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'Yes' : 'No') . "\n";
echo "</pre>";

// File permission checks
echo "<h2>File Permission Checks</h2>";
echo "<pre>";
$storagePath = __DIR__ . '/storage';
$bootstrapCachePath = __DIR__ . '/bootstrap/cache';
$envPath = __DIR__ . '/.env';

echo "Storage directory writable: " . (is_writable($storagePath) ? 'Yes' : 'No') . "\n";
echo "Bootstrap cache directory writable: " . (is_writable($bootstrapCachePath) ? 'Yes' : 'No') . "\n";
echo ".env file exists: " . (file_exists($envPath) ? 'Yes' : 'No') . "\n";
echo ".env file readable: " . (is_readable($envPath) ? 'Yes' : 'No') . "\n";
echo "</pre>";

// Laravel config check
echo "<h2>Laravel Config Check</h2>";
echo "<pre>";
if (file_exists($envPath)) {
    echo "Session driver set in .env: ";
    
    // Read SESSION_DRIVER from .env file
    $env = file_get_contents($envPath);
    preg_match('/SESSION_DRIVER=(.*)/', $env, $matches);
    if (isset($matches[1])) {
        echo trim($matches[1]) . "\n";
    } else {
        echo "Not found (will use default)\n";
    }
    
    // Check for APP_KEY
    preg_match('/APP_KEY=(.*)/', $env, $matches);
    echo "APP_KEY is " . (isset($matches[1]) && trim($matches[1]) !== '' ? 'set' : 'not set or empty') . "\n";
    
    // Check for auth config
    echo "\nContent of config/auth.php (if readable):\n";
    $authConfigPath = __DIR__ . '/config/auth.php';
    if (file_exists($authConfigPath) && is_readable($authConfigPath)) {
        // Try to extract and display relevant parts of the auth config
        $authConfig = file_get_contents($authConfigPath);
        
        // Extract defaults
        preg_match("/'defaults'\s*=>\s*\[\s*'guard'\s*=>\s*'([^']+)'/s", $authConfig, $guardMatches);
        echo "Default guard: " . (isset($guardMatches[1]) ? $guardMatches[1] : "Not found") . "\n";
        
        // Look for guards
        echo "\nConfigured guards:\n";
        preg_match("/'guards'\s*=>\s*\[(.*?)\],\s*'/s", $authConfig, $guardsMatches);
        if (isset($guardsMatches[1])) {
            echo htmlspecialchars($guardsMatches[1]) . "\n";
        } else {
            echo "Could not parse guards configuration\n";
        }
        
        // Look for providers
        echo "\nConfigured providers:\n";
        preg_match("/'providers'\s*=>\s*\[(.*?)\],\s*'/s", $authConfig, $providersMatches);
        if (isset($providersMatches[1])) {
            echo htmlspecialchars($providersMatches[1]) . "\n";
        } else {
            echo "Could not parse providers configuration\n";
        }
    } else {
        echo "Auth config file not found or not readable\n";
    }
} else {
    echo ".env file not found or not accessible\n";
}
echo "</pre>";

// Middleware check
echo "<h2>Middleware Issues Check</h2>";
echo "<pre>";
$kernelPath = __DIR__ . '/app/Http/Kernel.php';
if (file_exists($kernelPath) && is_readable($kernelPath)) {
    $kernel = file_get_contents($kernelPath);
    
    // Check redirectIfAuthenticated
    echo "RedirectIfAuthenticated middleware is ";
    if (strpos($kernel, 'RedirectIfAuthenticated') !== false) {
        echo "found in Kernel.php\n";
        
        // Check the alias
        preg_match("/'guest'\s*=>\s*[^,]+/", $kernel, $matches);
        if (isset($matches[0])) {
            echo "Guest middleware alias: " . $matches[0] . "\n";
        } else {
            echo "Guest middleware alias not found\n";
        }
    } else {
        echo "not found in Kernel.php\n";
    }
    
    // Check Authenticate middleware
    echo "Authenticate middleware is ";
    if (strpos($kernel, 'Authenticate') !== false) {
        echo "found in Kernel.php\n";
        
        // Check the alias
        preg_match("/'auth'\s*=>\s*[^,]+/", $kernel, $matches);
        if (isset($matches[0])) {
            echo "Auth middleware alias: " . $matches[0] . "\n";
        } else {
            echo "Auth middleware alias not found\n";
        }
    } else {
        echo "not found in Kernel.php\n";
    }
} else {
    echo "Kernel.php file not found or not readable\n";
}
echo "</pre>";

// Log check
echo "<h2>Recent Login Log Entries (if accessible)</h2>";
echo "<pre>";
$logPath = __DIR__ . '/storage/logs/laravel.log';

if (file_exists($logPath) && is_readable($logPath)) {
    $log = file_get_contents($logPath);
    
    // Try to extract recent login-related log entries
    $loginEntries = [];
    $redirectEntries = [];
    
    // Get the last 100 lines
    $lines = explode("\n", $log);
    $lines = array_slice($lines, -200);
    
    foreach ($lines as $line) {
        if (strpos($line, 'Login attempt') !== false || 
            strpos($line, 'login successful') !== false ||
            strpos($line, 'Authentication failed') !== false) {
            $loginEntries[] = htmlspecialchars($line);
        }
        
        if (strpos($line, 'Redirecting') !== false) {
            $redirectEntries[] = htmlspecialchars($line);
        }
    }
    
    if (!empty($loginEntries)) {
        echo "Recent login-related log entries:\n";
        foreach (array_slice($loginEntries, -10) as $entry) {
            echo $entry . "\n";
        }
    } else {
        echo "No recent login-related log entries found\n";
    }
    
    if (!empty($redirectEntries)) {
        echo "\nRecent redirect-related log entries:\n";
        foreach (array_slice($redirectEntries, -10) as $entry) {
            echo $entry . "\n";
        }
    } else {
        echo "\nNo recent redirect-related log entries found\n";
    }
} else {
    echo "Log file not found or not readable\n";
}
echo "</pre>";

echo "<p><strong>Security note:</strong> Remember to remove this file after debugging is complete as it exposes sensitive information.</p>"; 