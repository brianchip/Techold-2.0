<?php
/**
 * Safe Laravel Bootstrap Script
 * This script safely initializes Laravel after deployment
 */

echo "Starting Laravel bootstrap...\n";

// Check if we're in the right directory
if (!file_exists('artisan')) {
    echo "ERROR: Not in Laravel root directory\n";
    echo "Current directory: " . getcwd() . "\n";
    echo "Files in current directory: " . implode(', ', glob('*')) . "\n";
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
        echo "WARNING: No .env.example found. Creating minimal .env...\n";
        $minimalEnv = "APP_NAME=TecholdERP\n";
        $minimalEnv .= "APP_ENV=production\n";
        $minimalEnv .= "APP_KEY=\n";
        $minimalEnv .= "APP_DEBUG=false\n";
        $minimalEnv .= "APP_URL=" . (getenv('APP_URL') ?: 'http://localhost') . "\n";
        $minimalEnv .= "DB_CONNECTION=sqlite\n";
        $minimalEnv .= "DB_DATABASE=" . getcwd() . "/database/database.sqlite\n";
        file_put_contents('.env', $minimalEnv);
        echo "✓ Created minimal .env file\n";
    }
}

// Ensure SQLite database file exists
if (!file_exists('database/database.sqlite')) {
    echo "Creating SQLite database file...\n";
    if (!is_dir('database')) {
        mkdir('database', 0755, true);
    }
    touch('database/database.sqlite');
    echo "✓ SQLite database file created\n";
}

// Test Laravel bootstrap
echo "Testing Laravel bootstrap...\n";
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    echo "✓ Laravel bootstrap successful\n";
} catch (Exception $e) {
    echo "ERROR: Laravel bootstrap failed: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

// Only run artisan commands if everything is ready
echo "Running Laravel setup commands...\n";

$commands = [
    'php artisan key:generate --force' => 'Generate application key',
    'php artisan config:clear' => 'Clear config cache',
    'php artisan config:cache' => 'Cache configuration',
    'php artisan migrate --force' => 'Run database migrations'
];

foreach ($commands as $command => $description) {
    echo "Running: $description...\n";
    echo "Command: $command\n";
    $output = [];
    $return_code = 0;
    exec("$command 2>&1", $output, $return_code);
    
    if ($return_code === 0) {
        echo "✓ Success\n";
    } else {
        echo "✗ Failed with code $return_code\n";
        echo "Output: " . implode("\n", $output) . "\n";
        
        // For key generation, this is critical
        if (str_contains($command, 'key:generate')) {
            echo "CRITICAL: App key generation failed. This will cause 500 errors.\n";
        }
    }
    echo "---\n";
}

echo "Laravel bootstrap completed!\n";
echo "If you see any errors above, please fix them before using the application.\n";
?>
