#!/usr/bin/env bash
# ─────────────────────────────────────────────────────────────
# deploy.sh — Replai CRM one-command deploy
# Usage: bash deploy.sh
# ─────────────────────────────────────────────────────────────
set -e
APP="/var/www/html/chat.replai.id"
cd "$APP"

echo "▶ [1/7] Pull latest from GitHub..."
git pull origin main

echo "▶ [2/7] Composer install (production)..."
composer install --no-dev -o --quiet

echo "▶ [3/7] NPM build (frontend)..."
npm ci --quiet
npm run prod

echo "▶ [4/7] Bust .js.gz cache..."
find public -name "*.js.gz" -delete 2>/dev/null || true

echo "▶ [5/7] Database migrations..."
php artisan migrate --force

echo "▶ [6/7] Clear & rebuild caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
# Rebuild caches (route+view safe; config:cache skipped — env() used in app/)
php artisan route:cache
php artisan view:cache

echo "▶ [7/7] Restart queue workers..."
php artisan queue:restart 2>/dev/null || true

echo ""
echo "✅ Deploy selesai: $(git rev-parse --short HEAD) @ $(date '+%Y-%m-%d %H:%M:%S')"
