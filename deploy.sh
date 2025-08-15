#!/bin/bash

echo "Starting Laravel deployment..."

# Check if composer is available
if ! command -v composer &> /dev/null; then
    echo "Composer not found. Installing dependencies with downloaded composer..."
    php composer.phar install --no-dev --optimize-autoloader
else
    echo "Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader
fi

# Generate application key if not exists
if [ ! -f .env ]; then
    echo "Creating .env file from example..."
    cp .env.example .env
fi

echo "Generating application key..."
php artisan key:generate --force

# Clear and cache configurations
echo "Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

echo "Laravel deployment completed successfully!"
