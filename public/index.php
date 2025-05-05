<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/

// Hostinger Fix: Try multiple possible locations for the vendor directory
$paths = [
    __DIR__.'/../vendor/autoload.php',                          // Standard path
    __DIR__.'/vendor/autoload.php',                             // In public folder
    '/home/u569470620/domains/scriptqube.com/public_html/synergy/vendor/autoload.php', // Absolute path
    '/home/u569470620/domains/scriptqube.com/public_html/vendor/autoload.php',        // One directory up
];

$loaded = false;
foreach ($paths as $path) {
    if (file_exists($path)) {
        require $path;
        $loaded = true;
        break;
    }
}

if (!$loaded) {
    die('Could not find the vendor/autoload.php file. Please make sure Composer dependencies have been installed.
    Try running: <br><code>cd '.dirname(__DIR__).' && composer install</code><br>
    Paths checked:<br>'.implode('<br>', $paths));
}

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
