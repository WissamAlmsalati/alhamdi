#!/bin/sh
set -e

# Create .env from example if missing
if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Render's generateValue gives a random string without Laravel's "base64:" prefix; add it.
if [ -n "$APP_KEY" ] && [ "${APP_KEY#base64:}" = "$APP_KEY" ]; then
    export APP_KEY="base64:$(printf '%s' "$APP_KEY" | head -c 32 | base64)"
fi

# Generate app key if missing
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    php artisan key:generate --force
fi

# On Render, use the service's public URL when APP_URL isn't set.
if [ -z "$APP_URL" ] && [ -n "$RENDER_EXTERNAL_URL" ]; then
    export APP_URL="$RENDER_EXTERNAL_URL"
fi
if [ -n "$FRONTEND_HOST" ] && [ -z "$FRONTEND_URL" ]; then
    export FRONTEND_URL="https://$FRONTEND_HOST"
fi

# Wait for MySQL to be ready when using mysql driver
if [ "$DB_CONNECTION" = "mysql" ]; then
    echo "Waiting for MySQL at ${DB_HOST}:${DB_PORT}..."
    while ! nc -z "$DB_HOST" "$DB_PORT"; do
        sleep 1
    done
    echo "MySQL is ready."
fi

# Clear any config cache from the image build so runtime env vars are used
php artisan config:clear

# Set permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Run migrations
php artisan migrate --force

# Seed the database on first run if no categories exist yet
CATEGORY_COUNT=$(php artisan tinker --execute='echo App\Models\Category::count();' --no-interaction 2>/dev/null | grep -Eo '[0-9]+' | tail -n1)
if [ "$CATEGORY_COUNT" = "0" ]; then
    echo "Database is empty, running seeders..."
    php artisan db:seed --force
fi

# Create storage link. Remove any stale link first (e.g. one copied from a developer machine).
rm -f /var/www/html/public/storage
php artisan storage:link

# Restore the bundled catalog photos when they're missing. Hosts without a persistent disk
# (e.g. Render's free plan) wipe uploaded files on every restart, but the database keeps the paths.
mkdir -p /var/www/html/storage/app/public/products
for photo in /var/www/html/database/seeders/images/*; do
    [ -f "$photo" ] || continue
    target="/var/www/html/storage/app/public/products/$(basename "$photo")"
    [ -f "$target" ] || cp "$photo" "$target"
done
chown -R www-data:www-data /var/www/html/storage/app/public

# Clear and cache config; compile views on demand so code updates always show
php artisan config:cache
php artisan route:cache
php artisan view:clear

# Start Apache
exec apache2-foreground
