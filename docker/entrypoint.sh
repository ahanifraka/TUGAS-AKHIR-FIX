#!/bin/sh

set -e

# Wait for database to be ready
echo "Waiting for database connection..."
until nc -z -v -w30 db 3306; do
  echo "Waiting for database connection..."
  sleep 1
done
echo "Database is ready!"

# Ensure storage directory structure exists with proper permissions
echo "Ensuring storage directory structure exists..."
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/app/public

# Set proper permissions for mounted volumes (match php-fpm default user: www-data)
echo "Setting permissions for storage and public directories..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public
find /var/www/html/storage -type d -exec chmod 775 {} \;
find /var/www/html/storage -type f -exec chmod 664 {} \;
chmod -R 775 /var/www/html/bootstrap/cache

# Create storage symbolic link
php artisan storage:link

# Run Laravel setup commands
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Sync fresh frontend build assets into public volume (if mounted)
if [ -d "/opt/public_build" ]; then
  echo "Syncing frontend build assets into /var/www/html/public/build ..."
  rm -rf /var/www/html/public/build
  mkdir -p /var/www/html/public/build
  cp -a /opt/public_build/. /var/www/html/public/build/
  chown -R www-data:www-data /var/www/html/public/build || true
fi

# Sync fresh public images into public volume (if mounted)
if [ -d "/opt/public_images" ]; then
  echo "Syncing public images into /var/www/html/public/images ..."
  mkdir -p /var/www/html/public/images
  cp -a /opt/public_images/. /var/www/html/public/images/
  chown -R www-data:www-data /var/www/html/public/images || true
fi

# Run migrations (optional, uncomment if needed)
# php artisan migrate --force

# Start php-fpm
exec php-fpm