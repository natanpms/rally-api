#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

if [ ! -f .env ]; then
    echo "[entrypoint] .env not found, copying from .env.example"
    cp .env.example .env
fi

# The project directory is bind mounted in development, so the vendor directory
# built into the image may be shadowed by an empty one from the host.
if [ ! -f vendor/autoload.php ]; then
    echo "[entrypoint] vendor/ is missing, running composer install"
    composer install --prefer-dist --no-interaction
fi

if ! grep -qE '^APP_KEY=.+' .env; then
    echo "[entrypoint] generating APP_KEY"
    php artisan key:generate --force
fi

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

DB_HOST="${DB_HOST:-postgres}"
DB_PORT="${DB_PORT:-5432}"

echo "[entrypoint] waiting for PostgreSQL on ${DB_HOST}:${DB_PORT}"
until pg_isready --host="${DB_HOST}" --port="${DB_PORT}" --username="${DB_USERNAME:-postgres}" --quiet; do
    sleep 1
done
echo "[entrypoint] PostgreSQL is ready"

php artisan config:clear

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force
fi

exec "$@"
