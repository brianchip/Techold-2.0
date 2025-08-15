#!/bin/bash
set -e

echo "Custom build script - bypassing Laravel auto-detection"

# Install dependencies without running any Laravel commands
echo "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-scripts

# Create .env if needed
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env
fi

echo "Build completed - Laravel setup will happen in release phase"
