<?php
// OPcache Preload Script for Laravel 11
// Preloads all PHP files in these directories at PHP-FPM startup

$directories = [
    '/var/www/html/chat.replai.id/vendor/laravel/framework/src',
    '/var/www/html/chat.replai.id/vendor/illuminate',
    '/var/www/html/chat.replai.id/app',
    '/var/www/html/chat.replai.id/bootstrap',
    '/var/www/html/chat.replai.id/bootstrap/cache',
];

$count = 0;
foreach ($directories as $dir) {
    if (!is_dir($dir)) continue;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    foreach ($iterator as $file) {
        if ($file->getExtension() !== 'php') continue;
        $path = $file->getRealPath();
        // Skip test files and stubs
        if (strpos($path, '/tests/') !== false) continue;
        if (strpos($path, '/Test') !== false) continue;
        try {
            opcache_compile_file($path);
            $count++;
        } catch (Throwable $e) {
            // Skip files that fail to compile
        }
    }
}

// Log result
error_log("[OPcache Preload] Preloaded {$count} files");
