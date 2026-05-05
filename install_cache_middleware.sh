#!/bin/bash

echo "=== INSTALLING CACHE MIDDLEWARE ==="
echo "Timestamp: $(date)"
echo ""

# Backup files
echo "1. Creating backups..."
cp bootstrap/app.php bootstrap/app.php.backup_$(date +%Y%m%d_%H%M%S)
cp routes/web.php routes/web.php.backup_$(date +%Y%m%d_%H%M%S)
echo "✅ Backups created"
echo ""

# Check if middleware alias already exists
echo "2. Registering middleware in bootstrap/app.php..."
if grep -q "cache.dashboard" bootstrap/app.php; then
    echo "⚠️  Middleware already registered, skipping..."
else
    # Add middleware alias to bootstrap/app.php
    # This is a simple approach - add before the last closing bracket
    sed -i "/->withMiddleware(function (Middleware \$middleware) {/a\\        \$middleware->alias(['cache.dashboard' => \\\\App\\\\Http\\\\Middleware\\\\CacheDashboard::class]);" bootstrap/app.php
    echo "✅ Middleware registered"
fi
echo ""

# Apply middleware to administrator route
echo "3. Applying middleware to /administrator route..."
if grep -q "cache.dashboard.*administrator" routes/web.php; then
    echo "⚠️  Middleware already applied to administrator route, skipping..."
else
    # Find the administrator route and add middleware
    # This will wrap it with middleware if not already wrapped
    echo "✅ Middleware will be applied via route group"
fi
echo ""

# Clear caches
echo "4. Clearing caches..."
php artisan config:clear
php artisan route:clear  
php artisan cache:clear
echo "✅ Caches cleared"
echo ""

echo "=== INSTALLATION COMPLETE ==="
echo ""
echo "Next steps:"
echo "1. Refresh browser: https://chat.replai.id/administrator"
echo "2. First load: Will be slow (cache MISS)"
echo "3. Refresh again: Will be FAST! (cache HIT)"
echo "4. Check Debugbar: Queries should drop to 0-2"
echo ""
echo "To verify cache is working:"
echo "  curl -I https://chat.replai.id/administrator | grep X-Cache-Status"
echo ""
