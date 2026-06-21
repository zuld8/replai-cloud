#!/usr/bin/env bash
# deploy.sh — Replai CRM frontend deploy script
# Run: bash deploy.sh
set -e

APP=/var/www/html/chat.replai.id
cd $APP

echo "=== [1/7] Git pull ==="
git pull

echo "=== [2/7] Composer install ==="
composer install --no-dev -o --quiet

echo "=== [3/7] npm install + build ==="
npm ci --silent
npm run prod

echo "=== [4/7] Clean stale gz + old hashed chunks ==="
# With contenthash filenames, old hashed chunks accumulate → clean up
find public/js -name '*.gz' -delete
find public/css -name '*.gz' -delete 2>/dev/null || true
# Remove old hashed chunk files (keep non-hashed files like app-crm.js)
find public/js -maxdepth 1 -name '*.[a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9][a-f0-9].js' -delete 2>/dev/null || true

echo "=== [5/7] Regenerate gz for entry files ==="
# Only entry files need manual gz (chunks are hashed → new name each build → no stale gz)
for f in public/js/app*.js; do
    [ -f "$f" ] && gzip -9 -c "$f" > "${f}.gz" && echo "  gz: $f"
done

echo "=== [6/7] Clear caches ==="
php artisan optimize:clear
php artisan config:cache
php artisan route:cache

echo "=== [7/7] Restart queue ==="
php artisan queue:restart
sudo systemctl reload nginx 2>/dev/null || true

echo ""
echo "✅ Deploy complete!"
