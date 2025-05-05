<?php
/**
 * Laravel Deployment Helper for Hostinger
 * ----------------------------------------
 * This script helps set up proper permissions and performs
 * optimization tasks that might be needed on Hostinger.
 * 
 * IMPORTANT: Delete this file after successful deployment!
 */

// Basic security to prevent unauthorized access
$deploymentKey = ''; // Set a secure key here before using
$providedKey = $_GET['key'] ?? '';

// Always show plain text output
header('Content-Type: text/plain');

// Function to run artisan commands
function runArtisanCommand($command) {
    $output = [];
    $returnVar = 0;
    exec('php artisan ' . $command . ' 2>&1', $output, $returnVar);
    return [
        'success' => $returnVar === 0,
        'output' => implode("\n", $output)
    ];
}

// Function to set directory permissions
function setPermissions($dir, $permissions) {
    $success = chmod($dir, $permissions);
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                $path = $dir . "/" . $object;
                if (is_dir($path)) {
                    setPermissions($path, $permissions);
                } else {
                    chmod($path, $permissions);
                }
            }
        }
    }
    return $success;
}

// Security check
if (empty($deploymentKey) || $providedKey !== $deploymentKey) {
    die("ERROR: Please set a deployment key in the script and provide it as a 'key' parameter.");
}

echo "Starting Laravel Deployment Helper for Hostinger...\n\n";

// Check PHP version
echo "1. Checking PHP version...\n";
echo "   Current PHP version: " . PHP_VERSION . "\n";
if (version_compare(PHP_VERSION, '8.1.0', '<')) {
    echo "   WARNING: Laravel 10 requires PHP 8.1 or higher. Your current version may cause issues.\n\n";
} else {
    echo "   PHP version check passed.\n\n";
}

// Check if .env file exists
echo "2. Checking environment configuration...\n";
if (!file_exists(__DIR__ . '/.env')) {
    echo "   WARNING: No .env file found. Please create one based on .env.example.\n\n";
} else {
    echo "   .env file exists.\n\n";
}

// Set directory permissions
echo "3. Setting directory permissions...\n";
echo "   Setting project directory permissions to 755...\n";
setPermissions(__DIR__, 0755);
echo "   Setting storage directory permissions to 777...\n";
setPermissions(__DIR__ . '/storage', 0777);
echo "   Setting bootstrap/cache directory permissions to 777...\n";
setPermissions(__DIR__ . '/bootstrap/cache', 0777);
echo "   Permissions updated.\n\n";

// Clearing cache
echo "4. Clearing and rebuilding cache...\n";
echo runArtisanCommand('cache:clear')['output'] . "\n";
echo runArtisanCommand('config:clear')['output'] . "\n";
echo runArtisanCommand('view:clear')['output'] . "\n";
echo runArtisanCommand('route:clear')['output'] . "\n";
echo "   Cache cleared.\n\n";

// Optimizing application
echo "5. Optimizing application...\n";
echo runArtisanCommand('config:cache')['output'] . "\n";
echo runArtisanCommand('route:cache')['output'] . "\n";
echo runArtisanCommand('view:cache')['output'] . "\n";
echo "   Optimization completed.\n\n";

// Creating storage link
echo "6. Creating storage link...\n";
$storageResult = runArtisanCommand('storage:link');
echo $storageResult['output'] . "\n";
if (!$storageResult['success']) {
    echo "   WARNING: Could not create storage link automatically. You may need to create it manually or check permissions.\n\n";
} else {
    echo "   Storage link created successfully.\n\n";
}

// Check for proper .htaccess files
echo "7. Checking .htaccess files...\n";
if (!file_exists(__DIR__ . '/.htaccess')) {
    echo "   WARNING: No root .htaccess file found. Creating basic version...\n";
    file_put_contents(__DIR__ . '/.htaccess', "<IfModule mod_rewrite.c>\nRewriteEngine On\nRewriteRule ^(.*)$ public/$1 [L]\n</IfModule>");
    echo "   Basic .htaccess created.\n";
} else {
    echo "   Root .htaccess file exists.\n";
}

if (!file_exists(__DIR__ . '/public/.htaccess')) {
    echo "   WARNING: No public/.htaccess file found. This may cause routing issues.\n";
} else {
    echo "   Public .htaccess file exists.\n";
}
echo "\n";

// Generate application key if needed
echo "8. Checking application key...\n";
$envContent = file_get_contents(__DIR__ . '/.env');
if (strpos($envContent, 'APP_KEY=') !== false && strpos($envContent, 'APP_KEY=base64:') === false) {
    echo "   Application key is missing or empty. Generating new key...\n";
    echo runArtisanCommand('key:generate --force')['output'] . "\n";
    echo "   Application key generated.\n\n";
} else {
    echo "   Application key exists.\n\n";
}

echo "Laravel Deployment Helper completed!\n";
echo "------------------------------------\n";
echo "IMPORTANT SECURITY NOTICE: Delete this file (deploy.php) after successful deployment!\n";
?> 