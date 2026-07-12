#!/usr/bin/env bash
#
# Run this on the server AFTER the FTP deploy action has synced new source files.
# It installs deps, builds assets, runs migrations, and clears stale caches.
#
# Usage:
#   ./deploy.sh            # full run (composer + npm + build + migrate + cache clear)
#   ./deploy.sh --fast     # skip composer/npm install (only rebuild assets + clear cache)
#
set -euo pipefail

cd "$(dirname "$0")"

FAST=false
if [[ "${1:-}" == "--fast" ]]; then
    FAST=true
fi

echo "==> Deploying $(pwd)"

if [[ "$FAST" == false ]]; then
    echo "==> composer install"
    composer install --no-dev --optimize-autoloader

    echo "==> npm install"
    npm install
fi

echo "==> npm run build"
npm run build

echo "==> php artisan migrate --force"
php artisan migrate --force

echo "==> clearing caches"
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

echo "==> optimizing (config/route/view cache for production)"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> done"
