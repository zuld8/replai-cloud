<?php
$logFile = '/var/www/html/chat.replai.id/storage/logs/laravel-2026-04-08.log';
$writable = is_writable($logFile);
$owner = posix_getpwuid(fileowner($logFile));
$myUser = posix_getpwuid(posix_geteuid());
file_put_contents('/tmp/log_test.txt', json_encode([
    'log_writable' => $writable,
    'log_owner' => $owner['name'],
    'php_user' => $myUser['name'],
    'php_version' => phpversion(),
]));
// Try writing to log
try {
    \Illuminate\Support\Facades\Log::info('PHP_LOG_TEST', ['time' => date('H:i:s')]);
    file_put_contents('/tmp/log_test.txt', "\nLOG_SUCCESS", FILE_APPEND);
} catch (Throwable $e) {
    file_put_contents('/tmp/log_test.txt', "\nLOG_FAILED: " . $e->getMessage(), FILE_APPEND);
}
echo json_encode(['ok' => true]);
