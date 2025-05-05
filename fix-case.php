<?php

/**
 * This script fixes case sensitivity issues when deploying from Windows to Linux
 * To run: php fix-case.php
 */

// Define the controllers that need to be checked
$controllers = [
    'LoginController',
    'DashboardController',
    'CompanyController',
    'ReminderController',
    'RoleController',
    'PermissionController',
    'NoteController',
    'UserController',
    'UserModalController'
];

$controllersPath = __DIR__ . '/app/Http/Controllers/';

echo "Checking for case sensitivity issues in controllers...\n";

foreach ($controllers as $controller) {
    $correctFile = $controllersPath . $controller . '.php';
    $lowerFile = $controllersPath . strtolower($controller) . '.php';
    
    // Check if lower case file exists but correct case doesn't
    if (file_exists($lowerFile) && !file_exists($correctFile) && $lowerFile !== $correctFile) {
        echo "Found lowercase version of {$controller}.php\n";
        
        // Copy the content from lowercase to correct case
        $content = file_get_contents($lowerFile);
        
        // Ensure the class name inside matches the file name
        $content = preg_replace(
            '/class\s+' . preg_quote(strtolower($controller)) . '\s+/i',
            'class ' . $controller . ' ',
            $content
        );
        
        // Create the correctly cased file
        file_put_contents($correctFile, $content);
        echo "Created properly cased file: {$controller}.php\n";
        
        // Optionally remove the lowercase file
        // unlink($lowerFile);
        // echo "Removed lowercase file: " . strtolower($controller) . ".php\n";
    } elseif (file_exists($correctFile)) {
        echo "{$controller}.php exists with correct case.\n";
        
        // Check the class name inside
        $content = file_get_contents($correctFile);
        if (!preg_match('/class\s+' . preg_quote($controller) . '\s+/i', $content)) {
            echo "Warning: Class name inside {$controller}.php may not match file name.\n";
            
            // Fix the class name
            $content = preg_replace(
                '/class\s+\w+\s+extends\s+Controller/i',
                'class ' . $controller . ' extends Controller',
                $content
            );
            
            file_put_contents($correctFile, $content);
            echo "Fixed class name in {$controller}.php\n";
        }
    } else {
        echo "Warning: {$controller}.php not found in any case variant.\n";
    }
}

echo "\nDone checking controllers.\n";

// Clear Laravel caches
if (file_exists('artisan')) {
    echo "\nClearing Laravel caches...\n";
    system('php artisan optimize:clear');
    system('php artisan config:clear');
    system('php artisan route:clear');
    system('php artisan cache:clear');
    echo "Caches cleared.\n";
}

echo "\nProcess completed. Please check the output for any warnings.\n"; 