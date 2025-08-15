<?php
/**
 * Safe Laravel Bootstrap Script
 * This script safely initializes Laravel after deployment
 */

echo "Starting Laravel bootstrap...\n";

// Check if we're in the right directory
if (!file_exists('artisan')) {
    echo "ERROR: Not in Laravel root directory\n";
    exit(1);
}

// Check if vendor directory exists
if (!is_dir('vendor')) {
    echo "ERROR: Dependencies not installed. Run 'composer install' first.\n";
    exit(1);
}

// Create .env if it doesn't exist
if (!file_exists('.env')) {
    if (file_exists('.env.example')) {
        echo "Creating .env from .env.example...\n";
        copy('.env.example', '.env');
    } else {
        echo "WARNING: No .env.example found. You'll need to create .env manually.\n";
    }
}

// Test Laravel bootstrap
echo "Testing Laravel bootstrap...\n";
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    echo "✓ Laravel bootstrap successful\n";
} catch (Exception $e) {
    echo "ERROR: Laravel bootstrap failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Only run artisan commands if everything is ready
echo "Running Laravel setup commands...\n";

$commands = [
    'php artisan key:generate --force',
    'php artisan config:cache',
    'php artisan route:cache',
    'php artisan view:cache',
    'php artisan migrate --force'
];

foreach ($commands as $command) {
    echo "Running: $command\n";
    $output = [];
    $return_code = 0;
    exec("$command 2>&1", $output, $return_code);
    
    if ($return_code === 0) {
        echo "✓ Success\n";
    } else {
        echo "✗ Failed with code $return_code\n";
        echo "Output: " . implode("\n", $output) . "\n";
        // Don't exit on failure, continue with other commands
    }
}

echo "Laravel bootstrap completed!\n";
?>
