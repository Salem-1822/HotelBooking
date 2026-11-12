#!/bin/sh
set -e

echo "Starting HotelBooking deployment initialization..."

# 1. Symlink public storage
echo "Linking storage..."
php artisan storage:link || true

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
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

echo "HotelBooking initialization complete. Launching Apache..."
exec apache2-foreground

