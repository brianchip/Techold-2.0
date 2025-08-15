web: vendor/bin/heroku-php-apache2 public/
release: bash -c 'php artisan key:generate --force && php artisan config:cache && php artisan migrate --force'
