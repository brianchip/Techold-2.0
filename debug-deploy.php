<?php
/**
 * Deployment Debug Script
 * Upload this file to your web root to debug deployment issues
 */

echo "<h1>Laravel Deployment Debug</h1>";
echo "<pre>";

echo "=== ENVIRONMENT CHECKS ===\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Current Directory: " . getcwd() . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] ?? 'Not set' . "\n";

echo "\n=== FILE CHECKS ===\n";
echo "Artisan exists: " . (file_exists('artisan') ? 'YES' : 'NO') . "\n";
echo "Vendor autoload exists: " . (file_exists('vendor/autoload.php') ? 'YES' : 'NO') . "\n";
echo ".env exists: " . (file_exists('.env') ? 'YES' : 'NO') . "\n";
echo ".env.example exists: " . (file_exists('.env.example') ? 'YES' : 'NO') . "\n";

if (file_exists('.env')) {
    echo "\n=== ENVIRONMENT VARIABLES ===\n";
    $envContent = file_get_contents('.env');
    $lines = explode("\n", $envContent);
    foreach ($lines as $line) {
        if (trim($line) && !str_starts_with(trim($line), '#')) {
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                // Don't show sensitive values
                if (in_array($key, ['APP_KEY', 'DB_PASSWORD'])) {
                    $value = $value ? '[SET]' : '[NOT SET]';
                }
                echo "$key = $value\n";
            }
        }
    }
}

echo "\n=== LARAVEL BOOTSTRAP TEST ===\n";
try {
    if (file_exists('vendor/autoload.php')) {
        require_once 'vendor/autoload.php';
        echo "✓ Autoloader loaded\n";
        
        if (file_exists('bootstrap/app.php')) {
            $app = require_once 'bootstrap/app.php';
            echo "✓ Laravel app bootstrapped\n";
            
            // Test database connection
            try {
                $pdo = $app->make('db')->connection()->getPdo();
                echo "✓ Database connection successful\n";
            } catch (Exception $e) {
                echo "✗ Database connection failed: " . $e->getMessage() . "\n";
            }
            
        } else {
            echo "✗ bootstrap/app.php not found\n";
        }
    } else {
        echo "✗ vendor/autoload.php not found\n";
    }
} catch (Exception $e) {
    echo "✗ Laravel bootstrap failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== SUGGESTED FIXES ===\n";
if (!file_exists('.env')) {
    echo "1. Create .env file from .env.example\n";
}
if (!file_exists('vendor/autoload.php')) {
    echo "2. Run: composer install\n";
}
echo "3. Run: php artisan key:generate\n";
echo "4. Run: php artisan config:cache\n";
echo "5. Check database connection settings\n";

echo "</pre>";
?>
