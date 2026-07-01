#!/bin/bash
# deploy.sh — Replai CRM safe deploy
# Usage: bash deploy.sh
set -e
cd /var/www/html/chat.replai.id

echo "── Git pull ──"
git pull origin main

echo "── Build frontend ──"
npm run prod

echo "── Clear gz cache ──"
find public/js  -name "*.js.gz"  -delete 2>/dev/null || true
find public/css -name "*.css.gz" -delete 2>/dev/null || true
echo "  gz cleared"

echo "── Laravel cache ──"
php artisan view:clear 2>/dev/null || true
php artisan config:clear 2>/dev/null || true

echo ""
echo "✅ Done! Hard refresh (Ctrl+Shift+R) di browser."
