#!/bin/sh
set -e

echo "Starting HotelBooking deployment initialization..."

# 1. Ensure storage directory structure exists and symlink public storage cleanly
echo "Configuring public storage directory structure and symlinks..."
mkdir -p /var/www/html/storage/app/public/cities \
         /var/www/html/storage/app/public/profiles \
         /var/www/html/storage/app/public/hotels/covers \
         /var/www/html/storage/app/public/hotels/gallery \
         /var/www/html/storage/app/public/rooms/main \
         /var/www/html/storage/app/public/rooms/gallery \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs

rm -rf /var/www/html/public/storage
php artisan storage:link

# 2. Warm caches
echo "Optimizing configuration, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# 4. Safely seed demo / super admin accounts if required
echo "Ensuring seed data is seeded..."
php artisan db:seed --class=SuperAdminSeeder --force || true

# 5. Fix permissions for storage and bootstrap/cache after cache warming
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache || true

echo "HotelBooking initialization complete. Launching Apache..."
exec apache2-foreground
