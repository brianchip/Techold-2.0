<?php
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Current Directory: " . getcwd() . "\n";
echo "Artisan exists: " . (file_exists('artisan') ? 'YES' : 'NO') . "\n";
echo "Artisan readable: " . (is_readable('artisan') ? 'YES' : 'NO') . "\n";
echo "Vendor autoload exists: " . (file_exists('vendor/autoload.php') ? 'YES' : 'NO') . "\n";

if (file_exists('artisan')) {
    echo "Artisan file permissions: " . substr(sprintf('%o', fileperms('artisan')), -4) . "\n";
}

// Try to run artisan
echo "\nTrying to execute artisan...\n";
$output = [];
$return_code = 0;
exec('php artisan --version 2>&1', $output, $return_code);
echo "Return code: $return_code\n";
echo "Output: " . implode("\n", $output) . "\n";
?>
