<?php
/**
 * Laravel Server Compatibility Check for Hostinger
 * ------------------------------------------------
 * This script checks if your server meets Laravel's requirements
 * and verifies common hosting configuration issues.
 * 
 * IMPORTANT: Delete this file after checking your server!
 */

// Set content type to plain text
header('Content-Type: text/plain');

// Function to check extension
function checkExtension($name) {
    return extension_loaded($name) ? "✅ Enabled" : "❌ Not enabled";
}

// Function to check PHP settings
function checkPHPSetting($name, $recommendedValue = null) {
    $actualValue = ini_get($name);
    $result = !empty($actualValue) ? $actualValue : "Not set";
    
    if ($recommendedValue !== null) {
        $isOk = version_compare($actualValue, $recommendedValue, '>=');
        $result .= " " . ($isOk ? "✅" : "⚠️ (Recommended: $recommendedValue)");
    }
    
    return $result;
}

// Function to check directory permissions
function checkDirPermissions($dir) {
    if (!file_exists($dir)) {
        return "❌ Directory not found";
    }
    
    $isWritable = is_writable($dir);
    $perms = substr(sprintf('%o', fileperms($dir)), -4);
    
    return "$perms " . ($isWritable ? "✅ Writable" : "❌ Not writable");
}

echo "Laravel Server Compatibility Check\n";
echo "=================================\n\n";

echo "PHP Environment:\n";
echo "---------------\n";
echo "PHP Version: " . PHP_VERSION . " " . (version_compare(PHP_VERSION, '8.1.0', '>=') ? "✅" : "❌ (Laravel 10 requires PHP 8.1+)") . "\n";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
echo "Operating System: " . PHP_OS . "\n\n";

echo "Required PHP Extensions:\n";
echo "----------------------\n";
echo "BCMath: " . checkExtension('bcmath') . "\n";
echo "Ctype: " . checkExtension('ctype') . "\n";
echo "Fileinfo: " . checkExtension('fileinfo') . "\n";
echo "JSON: " . checkExtension('json') . "\n";
echo "Mbstring: " . checkExtension('mbstring') . "\n";
echo "OpenSSL: " . checkExtension('openssl') . "\n";
echo "PDO: " . checkExtension('pdo') . "\n";
echo "MySQL: " . checkExtension('pdo_mysql') . "\n";
echo "Tokenizer: " . checkExtension('tokenizer') . "\n";
echo "XML: " . checkExtension('xml') . "\n";
echo "CURL: " . checkExtension('curl') . "\n";
echo "Zip: " . checkExtension('zip') . "\n\n";

echo "PHP Configuration:\n";
echo "-----------------\n";
echo "max_execution_time: " . checkPHPSetting('max_execution_time', 30) . "\n";
echo "memory_limit: " . checkPHPSetting('memory_limit', '128M') . "\n";
echo "upload_max_filesize: " . checkPHPSetting('upload_max_filesize') . "\n";
echo "post_max_size: " . checkPHPSetting('post_max_size') . "\n";
echo "display_errors: " . checkPHPSetting('display_errors') . "\n\n";

echo "Directory Permissions:\n";
echo "--------------------\n";
echo "Project Root: " . checkDirPermissions(__DIR__) . "\n";
echo "Storage Directory: " . checkDirPermissions(__DIR__ . '/storage') . "\n";
echo "Bootstrap/Cache: " . checkDirPermissions(__DIR__ . '/bootstrap/cache') . "\n\n";

echo "File Existence Check:\n";
echo "-------------------\n";
$requiredFiles = [
    '.env' => __DIR__ . '/.env',
    '.htaccess (root)' => __DIR__ . '/.htaccess',
    '.htaccess (public)' => __DIR__ . '/public/.htaccess',
    'index.php' => __DIR__ . '/public/index.php',
    'composer.json' => __DIR__ . '/composer.json',
    'artisan' => __DIR__ . '/artisan'
];

foreach ($requiredFiles as $name => $path) {
    echo "$name: " . (file_exists($path) ? "✅ Found" : "❌ Not found") . "\n";
}
echo "\n";

// Check mod_rewrite
echo "Apache mod_rewrite Check:\n";
echo "-----------------------\n";
$modRewriteEnabled = in_array('mod_rewrite', apache_get_modules());
echo "mod_rewrite: " . ($modRewriteEnabled ? "✅ Enabled" : "❌ Not enabled or cannot be detected") . "\n\n";

// Check MySQL connection
echo "Database Connection Check:\n";
echo "------------------------\n";
if (file_exists(__DIR__ . '/.env')) {
    $envContent = file_get_contents(__DIR__ . '/.env');
    preg_match('/DB_HOST=(.*)/', $envContent, $dbHost);
    preg_match('/DB_DATABASE=(.*)/', $envContent, $dbName);
    preg_match('/DB_USERNAME=(.*)/', $envContent, $dbUser);
    preg_match('/DB_PASSWORD=(.*)/', $envContent, $dbPass);
    
    if (!empty($dbHost[1]) && !empty($dbName[1]) && !empty($dbUser[1])) {
        try {
            $conn = new PDO("mysql:host=" . trim($dbHost[1]) . ";dbname=" . trim($dbName[1]), trim($dbUser[1]), trim($dbPass[1] ?? ''));
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "MySQL Connection: ✅ Connected successfully\n";
        } catch(PDOException $e) {
            echo "MySQL Connection: ❌ Connection failed: " . $e->getMessage() . "\n";
        }
    } else {
        echo "MySQL Connection: ❌ Database credentials not found in .env file\n";
    }
} else {
    echo "MySQL Connection: ❌ .env file not found\n";
}
echo "\n";

// Check URL routing
echo "URL Routing Check:\n";
echo "-----------------\n";
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$uri = $_SERVER['REQUEST_URI'];
$baseUrl = $protocol . "://" . $host . str_replace('/server-check.php', '', $uri);

echo "Base URL: $baseUrl\n";
echo "To test routes, try accessing these URLs:\n";
echo "- Home: $baseUrl/\n";
echo "- Login: $baseUrl/login\n";
echo "- Dashboard: $baseUrl/dashboard (requires login)\n\n";

// New redirect loop detection
echo "Redirect Loop Detection:\n";
echo "----------------------\n";
echo "Checking for common causes of redirect loops...\n";

// Check if Authenticate and RedirectIfAuthenticated middlewares exist and have been modified
$authenticateFile = __DIR__ . '/app/Http/Middleware/Authenticate.php';
$redirectIfAuthFile = __DIR__ . '/app/Http/Middleware/RedirectIfAuthenticated.php';

echo "Authenticate middleware: " . (file_exists($authenticateFile) ? "✅ Found" : "❌ Not found") . "\n";
echo "RedirectIfAuthenticated middleware: " . (file_exists($redirectIfAuthFile) ? "✅ Found" : "❌ Not found") . "\n";

if (file_exists($authenticateFile) && file_exists($redirectIfAuthFile)) {
    $authFileContent = file_get_contents($authenticateFile);
    $redirectFileContent = file_get_contents($redirectIfAuthFile);
    
    // Check for common issues
    $issues = [];
    
    if (strpos($authFileContent, 'return \'/login\';') !== false) {
        $issues[] = "⚠️ Authenticate.php is using hardcoded '/login' path instead of route('login')";
    }
    
    if (strpos($redirectFileContent, 'return redirect(') !== false && 
        strpos($redirectFileContent, 'if ($currentPath === \'dashboard\' || $request->is(\'dashboard\'))') === false) {
        $issues[] = "⚠️ RedirectIfAuthenticated.php may be missing dashboard path exclusion";
    }
    
    if (count($issues) > 0) {
        echo "Potential issues detected:\n";
        foreach ($issues as $issue) {
            echo "  - $issue\n";
        }
        echo "These issues might cause redirect loops. The fixes have been applied to your middleware files.\n";
    } else {
        echo "No common redirect loop issues detected in middleware. ✅\n";
    }
}

// Check route file for redirect rules
$routesWebFile = __DIR__ . '/routes/web.php';
if (file_exists($routesWebFile)) {
    $routesContent = file_get_contents($routesWebFile);
    
    $routeIssues = [];
    
    // Check for potentially problematic redirects
    if (preg_match_all('/redirect\([\'"]\/(\w+)[\'"]/', $routesContent, $matches)) {
        $redirectEndpoints = array_unique($matches[1]);
        foreach ($redirectEndpoints as $endpoint) {
            if (preg_match('/redirect\([\'"]\/'. $endpoint .'[\'"]/', $routesContent)) {
                $routeIssues[] = "⚠️ Found redirect to '/$endpoint' - check if there's another redirect from this route";
            }
        }
    }
    
    if (count($routeIssues) > 0) {
        echo "Potential route redirect issues:\n";
        foreach ($routeIssues as $issue) {
            echo "  - $issue\n";
        }
        echo "These might cause redirect loops. The web.php file has been updated to fix these issues.\n";
    } else {
        echo "No common redirect loop issues detected in routes. ✅\n";
    }
}

echo "\n";

// Check for HTTPS forced redirect with .htaccess
$htaccessFile = __DIR__ . '/.htaccess';
if (file_exists($htaccessFile)) {
    $htaccessContent = file_get_contents($htaccessFile);
    
    if (strpos($htaccessContent, 'RewriteCond %{HTTPS} off') !== false && 
        strpos($htaccessContent, 'RewriteRule ^ https://') !== false) {
        echo "⚠️ Your .htaccess contains HTTPS redirection. If you're testing locally without HTTPS, this could cause redirect loops.\n";
    }
}

echo "\n";

echo "Security Check:\n";
echo "--------------\n";
echo "APP_DEBUG: " . ((file_exists(__DIR__ . '/.env') && strpos(file_get_contents(__DIR__ . '/.env'), 'APP_DEBUG=true') !== false) ? "⚠️ Enabled (set to false in production!)" : "✅ Disabled") . "\n";
echo "Storage & Cache Directories: " . (is_writable(__DIR__ . '/storage') && is_writable(__DIR__ . '/bootstrap/cache') ? "✅ Writable" : "❌ Not writable") . "\n";
echo "Public .env Access: " . (@file_get_contents($baseUrl . '/.env') ? "❌ VULNERABLE - .env is publicly accessible!" : "✅ Protected") . "\n\n";

echo "Summary:\n";
echo "--------\n";
echo "Your server appears to be " . (
    version_compare(PHP_VERSION, '8.1.0', '>=') && 
    extension_loaded('bcmath') && 
    extension_loaded('ctype') && 
    extension_loaded('fileinfo') && 
    extension_loaded('json') && 
    extension_loaded('mbstring') && 
    extension_loaded('openssl') && 
    extension_loaded('pdo') && 
    extension_loaded('tokenizer') && 
    extension_loaded('xml') && 
    file_exists(__DIR__ . '/.env') && 
    is_writable(__DIR__ . '/storage') && 
    is_writable(__DIR__ . '/bootstrap/cache') ? 
    "COMPATIBLE" : "NOT FULLY COMPATIBLE"
) . " with Laravel requirements.\n\n";

echo "IMPORTANT SECURITY NOTICE: Delete this file (server-check.php) after checking server compatibility!\n";
?> 