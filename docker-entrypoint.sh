#!/bin/sh
set -e

# APP_KEY, DB_*, etc. must be set as Railway environment variables.
# Generate a key once locally with: php artisan key:generate --show
if [ -z "$APP_KEY" ]; then
  echo "WARNING: APP_KEY is not set. Set it in Railway's Variables tab."
fi

php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run pending migrations on every deploy (safe to skip by removing this line
# if you prefer to run migrations manually)
php artisan migrate --force

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"