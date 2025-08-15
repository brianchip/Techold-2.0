#!/bin/bash

echo "Starting Laravel deployment..."
set -e  # Exit on any error

# Ensure we're in the right directory
if [ ! -f "artisan" ]; then
    echo "ERROR: artisan file not found. Are we in the Laravel root directory?"
    exit 1
fi

# Check if vendor directory exists
if [ ! -d "vendor" ]; then
    echo "ERROR: vendor directory not found. Dependencies need to be installed first."
    exit 1
fi

# Ensure .env file exists
if [ ! -f .env ]; then
    echo "Creating .env file from example..."
    if [ -f .env.example ]; then
        cp .env.example .env
    else
        echo "ERROR: .env.example file not found"
        exit 1
    fi
fi

# Test if artisan works
echo "Testing artisan command..."
if ! php artisan --version > /dev/null 2>&1; then
    echo "ERROR: artisan command failed"
    php artisan --version  # Show the actual error
    exit 1
fi

echo "Generating application key..."
php artisan key:generate --force

# Clear any cached config first
echo "Clearing caches..."
php artisan config:clear || true
php artisan cache:clear || true

# Optimize Laravel
echo "Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

echo "Laravel deployment completed successfully!"
