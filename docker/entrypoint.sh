#!/bin/bash
set -e

# Substitute PORT in nginx.conf if PORT is set (Railway sets $PORT)
if [ -n "$PORT" ]; then
    sed -i "s/listen [0-9]\+;/listen $PORT;/g" /etc/nginx/nginx.conf
fi

# Ensure correct permissions for storage and bootstrap cache
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# Storage link
php artisan storage:link --force 2>/dev/null || true

# Check if migrations should run
if [ "$RUN_MIGRATIONS" = "true" ] || [ -n "$DATABASE_URL" ] || [ -n "$PGHOST" ] || [ -n "$DB_HOST" ]; then
    echo "Running database migrations..."
    php artisan migrate --force 2>&1 || echo "Warning: Migration failed or DB not accessible yet."
fi

exec "$@"
