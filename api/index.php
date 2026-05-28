<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    require __DIR__ . '/../vendor/autoload.php';

    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // Serverless environments are read-only, so we move storage to /tmp
    $app->useStoragePath('/tmp/storage');
    
    // Also move bootstrap cache to /tmp to prevent read-only errors
    $app->useBootstrapPath('/tmp/bootstrap');

    // Ensure necessary storage directories exist in /tmp
    $directories = [
        '/tmp/storage/framework/views',
        '/tmp/storage/framework/cache/data',
        '/tmp/storage/framework/sessions',
        '/tmp/storage/logs',
        '/tmp/bootstrap/cache',
    ];

    foreach ($directories as $directory) {
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
    }

    $app->handleRequest(Illuminate\Http\Request::capture());
} catch (\Throwable $e) {
    echo "<div style='font-family: sans-serif; padding: 20px; background: #fee; color: #900; border: 1px solid #c00; border-radius: 5px;'>";
    echo "<h2>🚨 Vercel Deployment Error</h2>";
    echo "<strong>Message:</strong> " . $e->getMessage() . "<br><br>";
    echo "<strong>File:</strong> " . $e->getFile() . " on line " . $e->getLine() . "<br><br>";
    echo "<strong>Stack Trace:</strong><br>";
    echo "<pre style='background: #fff; padding: 10px; border-radius: 5px; overflow-x: auto;'>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}
