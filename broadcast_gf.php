#!/usr/bin/env php
<?php
/*
 * ============================================================
 *  broadcast_gf.php — Broadcast Mingguan Golden Future IDN
 * ============================================================
 *  Buat 15 template Meta + 15 broadcast dalam satu command.
 *
 *  Penggunaan:
 *    php broadcast_gf.php \
 *      --campaign="namacamp" \
 *      --video="/tmp/video.mp4" \
 *      --narasi-file="/tmp/narasi.txt" \
 *      --schedule="2026-09-11 10:00" \
 *      [--dry-run]
 *
 *  Atau narasi langsung (tanpa newline):
 *    php broadcast_gf.php \
 *      --campaign="namacamp" \
 *      --video="/tmp/video.mp4" \
 *      --narasi="teks body template..." \
 *      --schedule="2026-09-11 10:00"
 * ============================================================
 */

date_default_timezone_set('Asia/Jakarta');
error_reporting(E_ALL);

// ============================================================
// KONFIGURASI GOLDEN FUTURE IDN
// ============================================================
$CONFIG = [
    'db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'whatsmail',
        'user' => 'root',
        'pass' => 'Goldenreog20',
    ],
    'app_path'    => '/var/www/html/chat.replai.id',
    'app_url'     => 'https://chat.replai.id',
    'meta_api'    => 'https://graph.facebook.com/v22.0',
    'business_id' => '229ae208-6270-483d-b6f3-14948abdf508',
    'merchant_id' => '31920758-a779-4a96-bcfa-4fc909b14d14',

    // Dua akun WABA
    'waba' => [
        'waba1' => [
            'label'           => 'WABA1 (559)',
            'meta_account_id' => '16fb961b-4a37-4180-803b-86520daa7633',
            'waba_biz_id'     => '24241996812106703',
            'app_id'          => '1090624879835883',
            'device_id'       => 'f122986c-bc48-4238-b35c-d22d4c932258',
        ],
        'waba2' => [
            'label'           => 'WABA2 (828)',
            'meta_account_id' => '95997440-7d90-42d8-9710-db97ef8f9430',
            'waba_biz_id'     => '2015025269240863',
            'app_id'          => '1470842094233125',
            'device_id'       => 'e6bfd1d5-8b49-4a32-9818-c9dba5079878',
        ],
    ],

    // 15 kategori kontak: suffix → category_id
    'categories' => [
        'd'  => 'b0d0a79c-94c9-425b-b42b-0bd6cc936ebe',
        '1'  => 'f623ade9-5512-4792-a969-48065aecb771',
        '2'  => '95d2494a-f523-4dad-b2ad-e7d3d564e61e',
        '3'  => '4dca875e-7d7c-4ecc-ab8c-62ed0f6f717a',
        '4'  => '24f1ddde-826a-4597-9bbd-94f121351865',
        '5'  => '1bcc6090-8b35-4057-b1c4-7f36327e7edd',
        '6'  => '83aa19bd-7721-49da-a028-ae581d85baee',
        '7'  => '55c3202c-170a-49f7-930b-161b586e7fb3',
        '8'  => 'fb66737c-6fda-4d34-9b4d-4cb32c16174c',
        '9'  => '5f59c7f1-51b1-4dc0-863e-510af11b11aa',
        '10' => '0201005e-6bf8-483b-9256-cc2aa8cd37df',
        '11' => '7051cce2-0dee-4b85-968d-88d80a7d33fa',
        '12' => '4881b9a0-f7b8-4da7-b1c2-b30d101da0b9',
        '13' => 'be1233d9-eed1-4d40-a9bb-586ee5768b92',
        '14' => 'c47e31d6-8401-4499-9384-d8b73f33de02',
    ],

    // Urutan kirim: WABA2 duluan, lalu WABA1
    'broadcast_order' => [
        ['waba' => 'waba2', 'suffix' => 'd'],
        ['waba' => 'waba2', 'suffix' => '2'],
        ['waba' => 'waba2', 'suffix' => '4'],
        ['waba' => 'waba2', 'suffix' => '6'],
        ['waba' => 'waba2', 'suffix' => '8'],
        ['waba' => 'waba2', 'suffix' => '10'],
        ['waba' => 'waba2', 'suffix' => '12'],
        ['waba' => 'waba2', 'suffix' => '14'],
        ['waba' => 'waba1', 'suffix' => '1'],
        ['waba' => 'waba1', 'suffix' => '3'],
        ['waba' => 'waba1', 'suffix' => '5'],
        ['waba' => 'waba1', 'suffix' => '7'],
        ['waba' => 'waba1', 'suffix' => '9'],
        ['waba' => 'waba1', 'suffix' => '11'],
        ['waba' => 'waba1', 'suffix' => '13'],
    ],
];

// ============================================================
// HELPER FUNCTIONS
// ============================================================

/**
 * Generate UUID v4 tanpa library external
 */
function uuid4(): string
{
    $data = random_bytes(16);
    // Atur bit versi (4) dan variant (RFC 4122)
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function log_info(string $msg): void  { fwrite(STDOUT, "\033[36m[INFO]\033[0m  $msg\n"); }
function log_warn(string $msg): void  { fwrite(STDOUT, "\033[33m[WARN]\033[0m  $msg\n"); }
function log_error(string $msg): void { fwrite(STDERR, "\033[31m[ERROR]\033[0m $msg\n"); }
function log_ok(string $msg): void    { fwrite(STDOUT, "\033[32m[OK]\033[0m    $msg\n"); }
function log_step(string $msg): void  { fwrite(STDOUT, "\n\033[1;35m=== $msg ===\033[0m\n"); }

function formatBytes(int $bytes): string
{
    if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
    if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
    return $bytes . ' B';
}

// ============================================================
// DATABASE
// ============================================================

function getDb(array $dbConfig): PDO
{
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        $dbConfig['host'], $dbConfig['port'], $dbConfig['name']);
    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    return $pdo;
}

/**
 * Ambil access_token dari tabel meta_accounts
 */
function getAccessToken(PDO $pdo, string $metaAccountId): string
{
    $stmt = $pdo->prepare('SELECT access_token FROM meta_accounts WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $metaAccountId]);
    $row = $stmt->fetch();
    if (!$row || empty($row['access_token'])) {
        throw new RuntimeException("Access token tidak ditemukan untuk meta_account_id: $metaAccountId");
    }
    return $row['access_token'];
}

// ============================================================
// META API (cURL)
// ============================================================

/**
 * Wrapper cURL untuk Meta Graph API dengan error handling
 */
function metaCurl(string $url, array $opts = []): array
{
    $method  = $opts['method'] ?? 'GET';
    $headers = $opts['headers'] ?? [];
    $body    = $opts['body'] ?? null;
    $timeout = $opts['timeout'] ?? 30;
    $rawBody = $opts['raw_body'] ?? false;

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($body !== null) {
            // Body bisa JSON string atau binary
            curl_setopt($ch, CURLOPT_POSTFIELDS, $rawBody ? $body : json_encode($body));
        }
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error    = curl_error($ch);
    curl_close($ch);

    if ($error) {
        throw new RuntimeException("cURL error: $error");
    }

    $data = json_decode($response, true);
    if ($httpCode >= 400) {
        $errMsg = $data['error']['message'] ?? $response;
        $errCode = $data['error']['code'] ?? $httpCode;
        throw new RuntimeException("Meta API error ($errCode): $errMsg");
    }

    return $data ?? [];
}

/**
 * Resumable Upload video ke Meta → return handle string
 * Satu kali per WABA (handle bisa dipakai untuk semua template di WABA yang sama)
 */
function metaResumableUpload(string $apiBase, string $appId, string $token, string $videoPath): string
{
    $fileSize = filesize($videoPath);
    log_info("Upload video ke Meta ($appId) — " . formatBytes($fileSize));

    // Langkah 1: Buat upload session
    $sessionUrl = "$apiBase/$appId/uploads"
        . "?file_length=$fileSize"
        . "&file_type=" . urlencode('video/mp4')
        . "&access_token=" . urlencode($token);

    $session = metaCurl($sessionUrl, ['method' => 'POST']);
    $uploadId = $session['id'] ?? null;
    if (!$uploadId) {
        throw new RuntimeException("Gagal buat upload session: " . json_encode($session));
    }
    log_info("Upload session: $uploadId");

    // Langkah 2: Upload binary video
    $binary = file_get_contents($videoPath);
    $uploadUrl = "$apiBase/$uploadId";
    $handleResp = metaCurl($uploadUrl, [
        'method'   => 'POST',
        'headers'  => [
            "Authorization: OAuth $token",
            "file_offset: 0",
            "Content-Type: application/octet-stream",
        ],
        'body'     => $binary,
        'raw_body' => true,
        'timeout'  => 120, // Video bisa besar, beri waktu
    ]);

    $handle = $handleResp['h'] ?? null;
    if (!$handle) {
        throw new RuntimeException("Gagal upload binary: " . json_encode($handleResp));
    }
    log_ok("Handle: " . substr($handle, 0, 30) . "...");

    return $handle;
}

/**
 * Buat template di Meta Graph API
 * Return: ['id' => meta_template_id, 'status' => 'APPROVED'|...]
 */
function metaCreateTemplate(
    string $apiBase, string $wabaBizId, string $token,
    string $name, string $handle, string $narasi
): array {
    $url = "$apiBase/$wabaBizId/message_templates";
    $payload = [
        'name'       => $name,
        'category'   => 'MARKETING',
        'language'   => 'id',
        'components' => [
            [
                'type'    => 'HEADER',
                'format'  => 'VIDEO',
                'example' => [
                    'header_handle' => [$handle],
                ],
            ],
            [
                'type' => 'BODY',
                'text' => $narasi,
            ],
        ],
    ];

    log_info("Buat template: $name → WABA $wabaBizId");
    $resp = metaCurl($url, [
        'method'  => 'POST',
        'headers' => [
            "Authorization: Bearer $token",
            "Content-Type: application/json",
        ],
        'body'    => $payload,
        'timeout' => 30,
    ]);

    $metaId = $resp['id'] ?? null;
    $status = $resp['status'] ?? 'UNKNOWN';
    if (!$metaId) {
        throw new RuntimeException("Gagal buat template '$name': " . json_encode($resp));
    }

    return ['meta_id' => $metaId, 'status' => $status];
}

/**
 * Cek status template di Meta (polling sampai APPROVED)
 */
function metaWaitApproved(string $apiBase, string $metaId, string $token, int $maxRetry = 10): string
{
    for ($i = 0; $i < $maxRetry; $i++) {
        try {
            $url = "$apiBase/$metaId?fields=status&access_token=" . urlencode($token);
            $resp = metaCurl($url);
            $status = $resp['status'] ?? 'UNKNOWN';
            if ($status === 'APPROVED') return 'APPROVED';
            if (in_array($status, ['REJECTED', 'DISABLED'])) {
                log_warn("Template $metaId status: $status — SKIP");
                return $status;
            }
            // Masih PENDING, tunggu
            log_info("Template $metaId status: $status — tunggu 3 detik...");
            sleep(3);
        } catch (Exception $e) {
            log_warn("Gagal cek status template: " . $e->getMessage());
            sleep(3);
        }
    }
    return 'TIMEOUT';
}

// ============================================================
// TEMPLATE & BROADCAST DB OPERATIONS
// ============================================================

/**
 * Insert ke tabel message_templates
 */
function insertTemplate(PDO $pdo, array $data): string
{
    $id = uuid4();
    $now = date('Y-m-d H:i:s');
    $stmt = $pdo->prepare('
        INSERT INTO message_templates
            (id, business_id, merchant_id, meta_account_id, meta_id, name,
             category, language, message, type_content, image,
             waba_status_template, status, created_at, updated_at)
        VALUES
            (:id, :business_id, :merchant_id, :meta_account_id, :meta_id, :name,
             :category, :language, :message, :type_content, :image,
             :waba_status, :status, :created_at, :updated_at)
    ');
    $stmt->execute([
        ':id'              => $id,
        ':business_id'     => $data['business_id'],
        ':merchant_id'     => $data['merchant_id'],
        ':meta_account_id' => $data['meta_account_id'],
        ':meta_id'         => $data['meta_id'],
        ':name'            => $data['name'],
        ':category'        => 'MARKETING',
        ':language'        => 'id',
        ':message'         => $data['narasi'],
        ':type_content'    => 'video',
        ':image'           => $data['video_path'],
        ':waba_status'     => $data['status'],
        ':status'          => 'active',
        ':created_at'      => $now,
        ':updated_at'      => $now,
    ]);
    return $id;
}

/**
 * Insert ke tabel blash_whatsapps (header broadcast)
 */
function insertBroadcast(PDO $pdo, array $data): string
{
    $id = uuid4();
    $now = date('Y-m-d H:i:s');
    $stmt = $pdo->prepare('
        INSERT INTO blash_whatsapps
            (id, business_id, merchant_id, category_id, template_id, name,
             devices, meta_account_id, `use`, waba, status, schedule,
             delay, stop_sending, rest_sending, whatsapp_sender_notif,
             stat_total, stat_sent, stat_failed, file, metadata,
             created_at, updated_at)
        VALUES
            (:id, :business_id, :merchant_id, :category_id, :template_id, :name,
             :devices, :meta_account_id, :use, :waba, :status, :schedule,
             :delay, :stop_sending, :rest_sending, :wsn,
             :stat_total, 0, 0, :file, :metadata,
             :created_at, :updated_at)
    ');
    $stmt->execute([
        ':id'              => $id,
        ':business_id'     => $data['business_id'],
        ':merchant_id'     => $data['merchant_id'],
        ':category_id'     => $data['category_id'],
        ':template_id'     => $data['template_id'],
        ':name'            => $data['name'],
        ':devices'         => $data['device_id'],
        ':meta_account_id' => $data['meta_account_id'],
        ':use'             => 'whatsapp',
        ':waba'            => 'yes',
        ':status'          => 'pending',
        ':schedule'        => $data['schedule'],
        ':delay'           => 0,
        ':stop_sending'    => 0,
        ':rest_sending'    => 0,
        ':wsn'             => 'sequence',
        ':stat_total'      => $data['stat_total'],
        ':file'            => $data['file'],
        ':metadata'        => $data['metadata'],
        ':created_at'      => $now,
        ':updated_at'      => $now,
    ]);
    return $id;
}

/**
 * Ambil kontak dari stores dan bulk-insert ke blash_details
 * Return: jumlah kontak yang di-insert
 */
function populateDetails(PDO $pdo, string $blashId, string $deviceId, string $categoryId, string $businessId): int
{
    // Ambil semua kontak dari kategori ini
    $stmt = $pdo->prepare('
        SELECT id, phone, bsuid
        FROM stores
        WHERE category_id = :cat_id
          AND business_id = :biz_id
          AND phone IS NOT NULL
          AND phone != ""
          AND (waba_blocked = 0 OR waba_blocked IS NULL)
    ');
    $stmt->execute([':cat_id' => $categoryId, ':biz_id' => $businessId]);
    $contacts = $stmt->fetchAll();

    if (empty($contacts)) {
        log_warn("Tidak ada kontak untuk kategori $categoryId");
        return 0;
    }

    // Batch insert per 500 kontak (efisien untuk ~5000 kontak per kategori)
    $chunks = array_chunk($contacts, 500);
    $now = date('Y-m-d H:i:s');
    $total = 0;

    foreach ($chunks as $chunk) {
        $placeholders = [];
        $values = [];
        foreach ($chunk as $contact) {
            $detailId = uuid4();
            $placeholders[] = '(?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $values = array_merge($values, [
                $detailId,
                $blashId,
                $contact['id'],
                $contact['phone'],
                $contact['bsuid'] ?? null,
                $deviceId,
                'whatsapp',  // type
                'no',        // sending_status
                $now,        // created_at
            ]);
        }
        $sql = 'INSERT INTO blash_details
                    (id, blash_whatsapp_id, store_id, phone, bsuid, device_id, type, sending_status, created_at)
                VALUES ' . implode(', ', $placeholders);
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        $total += count($chunk);
    }

    return $total;
}

// ============================================================
// MAIN
// ============================================================

function main(int $argc, array $argv): void
{
    global $CONFIG;

    // --- Parse CLI arguments ---
    $opts = getopt('', ['campaign:', 'video:', 'narasi:', 'narasi-file:', 'schedule:', 'dry-run', 'templates-only']);

    $campaign      = $opts['campaign'] ?? null;
    $videoPath     = $opts['video'] ?? null;
    $schedule      = $opts['schedule'] ?? null;
    $dryRun        = isset($opts['dry-run']);
    $templatesOnly = isset($opts['templates-only']);

    // Narasi dari file atau langsung
    $narasi = null;
    if (!empty($opts['narasi-file'])) {
        $narasiFile = $opts['narasi-file'];
        if (!file_exists($narasiFile)) {
            log_error("File narasi tidak ditemukan: $narasiFile");
            exit(1);
        }
        $narasi = trim(file_get_contents($narasiFile));
    } elseif (!empty($opts['narasi'])) {
        $narasi = $opts['narasi'];
    }

    // --- Validasi input ---
    $errors = [];
    if (!$campaign)  $errors[] = "--campaign wajib diisi (contoh: hadirkanair)";
    if (!$videoPath) $errors[] = "--video wajib diisi (path video di server)";
    if (!$narasi)    $errors[] = "--narasi atau --narasi-file wajib diisi";
    if (!$schedule && !$templatesOnly) $errors[] = "--schedule wajib diisi (atau pakai --templates-only)";

    if (!empty($errors)) {
        log_error("Parameter tidak lengkap:");
        foreach ($errors as $e) log_error("  • $e");
        echo "\nPenggunaan:\n";
        echo "  php broadcast_gf.php --campaign=\"nama\" --video=\"/tmp/video.mp4\" --narasi-file=\"/tmp/narasi.txt\" --schedule=\"2026-09-11 10:00\" [--dry-run]\n\n";
        exit(1);
    }

    // Validasi campaign name (harus lowercase, alfanumerik + underscore)
    $campaign = strtolower(trim($campaign));
    if (!preg_match('/^[a-z0-9_]+$/', $campaign)) {
        log_error("Campaign name harus lowercase alfanumerik + underscore. Got: '$campaign'");
        exit(1);
    }

    // Validasi video file
    if (!file_exists($videoPath)) {
        log_error("File video tidak ditemukan: $videoPath");
        exit(1);
    }

    // Validasi schedule format (skip kalau templates-only)
    $scheduleTime = null;
    if (!$templatesOnly) {
        $scheduleTime = DateTime::createFromFormat('Y-m-d H:i', $schedule);
        if (!$scheduleTime) {
            log_error("Format schedule salah. Gunakan: 'YYYY-MM-DD HH:MM' (contoh: 2026-09-11 10:00)");
            exit(1);
        }
    }

    // --- Header info ---
    echo "\n";
    echo "╔══════════════════════════════════════════════════════╗\n";
    echo "║  BROADCAST MINGGUAN GOLDEN FUTURE IDN               ║\n";
    echo "╚══════════════════════════════════════════════════════╝\n";
    echo "\n";
    log_info("Campaign    : $campaign");
    log_info("Video       : $videoPath (" . formatBytes(filesize($videoPath)) . ")");
    log_info("Narasi      : " . mb_substr($narasi, 0, 80) . (mb_strlen($narasi) > 80 ? '...' : ''));
    if (!$templatesOnly) {
        log_info("Schedule    : $schedule WIB");
    }
    $modeLabel = $dryRun ? 'DRY RUN' : ($templatesOnly ? 'TEMPLATES ONLY (tanpa broadcast)' : 'LIVE');
    log_info("Mode        : $modeLabel");
    echo "\n";

    if ($dryRun) {
        log_warn("=== DRY RUN MODE — tidak ada perubahan yang dilakukan ===");
    }

    // --- Koneksi DB ---
    log_step("STEP 0: KONEKSI DATABASE");
    try {
        $pdo = getDb($CONFIG['db']);
        log_ok("Database connected");
    } catch (Exception $e) {
        log_error("Gagal koneksi DB: " . $e->getMessage());
        exit(1);
    }

    // Ambil access token untuk kedua WABA
    $tokens = [];
    foreach (['waba1', 'waba2'] as $wabaKey) {
        $metaAccId = $CONFIG['waba'][$wabaKey]['meta_account_id'];
        try {
            $tokens[$wabaKey] = getAccessToken($pdo, $metaAccId);
            log_ok($CONFIG['waba'][$wabaKey]['label'] . " — token OK (" . mb_substr($tokens[$wabaKey], 0, 20) . "...)");
        } catch (Exception $e) {
            log_error($e->getMessage());
            exit(1);
        }
    }

    // --- Copy video ke public path ---
    log_step("STEP 1: COPY VIDEO KE PUBLIC PATH");
    $videoUuid = uuid4();
    $videoFilename = $videoUuid . '.mp4';
    $videoRelPath  = '/uploads/template/' . $videoFilename;                         // Path di DB
    $videoAbsPath  = $CONFIG['app_path'] . '/public/uploads/template/' . $videoFilename; // Path fisik
    $videoUrl      = $CONFIG['app_url'] . '/uploads/template/' . $videoFilename;    // URL publik

    if (!$dryRun) {
        // Pastikan direktori target ada
        $targetDir = dirname($videoAbsPath);
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        if (!copy($videoPath, $videoAbsPath)) {
            log_error("Gagal copy video ke: $videoAbsPath");
            exit(1);
        }
        chmod($videoAbsPath, 0644);
        log_ok("Video → $videoAbsPath");
    } else {
        log_info("[DRY] Copy $videoPath → $videoAbsPath");
    }
    log_info("URL: $videoUrl");

    // --- Upload video ke Meta (1x per WABA, hemat bandwidth) ---
    log_step("STEP 2: UPLOAD VIDEO KE META (RESUMABLE UPLOAD)");
    $handles = [];
    foreach (['waba1', 'waba2'] as $wabaKey) {
        $appId = $CONFIG['waba'][$wabaKey]['app_id'];
        $label = $CONFIG['waba'][$wabaKey]['label'];
        if (!$dryRun) {
            try {
                $handles[$wabaKey] = metaResumableUpload(
                    $CONFIG['meta_api'], $appId, $tokens[$wabaKey], $videoPath
                );
                log_ok("$label — handle OK");
            } catch (Exception $e) {
                log_error("$label — gagal upload: " . $e->getMessage());
                exit(1);
            }
        } else {
            $handles[$wabaKey] = 'DRY_RUN_HANDLE_' . $wabaKey;
            log_info("[DRY] $label — skip upload");
        }
    }

    // --- Buat 15 template di Meta + simpan ke DB ---
    log_step("STEP 3: BUAT 15 TEMPLATE META");
    $templateIds = []; // suffix → internal template UUID
    $templateCount = 0;
    $failedTemplates = [];

    foreach ($CONFIG['broadcast_order'] as $item) {
        $wabaKey = $item['waba'];
        $suffix  = $item['suffix'];
        $tplName = $campaign . '_' . $suffix;
        $label   = $CONFIG['waba'][$wabaKey]['label'];
        $wabaBizId = $CONFIG['waba'][$wabaKey]['waba_biz_id'];
        $metaAccId = $CONFIG['waba'][$wabaKey]['meta_account_id'];

        if (!$dryRun) {
            try {
                // Buat template di Meta
                $result = metaCreateTemplate(
                    $CONFIG['meta_api'], $wabaBizId, $tokens[$wabaKey],
                    $tplName, $handles[$wabaKey], $narasi
                );

                $metaId = $result['meta_id'];
                $status = $result['status'];

                // Kalau belum APPROVED, polling (biasanya instan)
                if ($status !== 'APPROVED') {
                    $status = metaWaitApproved($CONFIG['meta_api'], $metaId, $tokens[$wabaKey]);
                }

                // Simpan ke DB message_templates
                $tplUuid = insertTemplate($pdo, [
                    'business_id'     => $CONFIG['business_id'],
                    'merchant_id'     => $CONFIG['merchant_id'],
                    'meta_account_id' => $metaAccId,
                    'meta_id'         => $metaId,
                    'name'            => $tplName,
                    'narasi'          => $narasi,
                    'video_path'      => $videoRelPath,
                    'status'          => $status,
                ]);

                $templateIds[$suffix] = $tplUuid;
                $templateCount++;
                log_ok("$tplName → $label | Meta ID: $metaId | Status: $status");

            } catch (Exception $e) {
                log_error("$tplName → GAGAL: " . $e->getMessage());
                $failedTemplates[] = $tplName;
            }
        } else {
            $tplUuid = uuid4();
            $templateIds[$suffix] = $tplUuid;
            $templateCount++;
            log_info("[DRY] $tplName → $label | Template UUID: $tplUuid");
        }

        // Jeda antar template creation biar nggak kena rate limit
        if (!$dryRun) usleep(500000); // 0.5 detik
    }

    echo "\n";
    log_info("Template berhasil: $templateCount / 15");
    if (!empty($failedTemplates)) {
        log_warn("Template gagal: " . implode(', ', $failedTemplates));
    }

    // Kalau ada template gagal, tanya mau lanjut atau tidak
    if (!empty($failedTemplates) && !$dryRun) {
        log_warn("Ada template yang gagal. Broadcast untuk template gagal akan di-skip.");
    }

    // --- Buat 15 broadcast + populate blash_details ---
    log_step("STEP 4: BUAT 15 BROADCAST");
    $metadata = json_encode([
        'header'  => ['format' => 'VIDEO', 'parameters' => []],
        'body'    => ['text' => $narasi, 'parameters' => []],
        'footer'  => ['text' => ''],
        'buttons' => [],
        'media'   => 1,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $broadcastCount = 0;
    $totalKontak = 0;
    $scheduleBase = clone $scheduleTime;

    foreach ($CONFIG['broadcast_order'] as $idx => $item) {
        $wabaKey    = $item['waba'];
        $suffix     = $item['suffix'];
        $bcName     = $campaign . '_' . $suffix;
        $categoryId = $CONFIG['categories'][$suffix];
        $label      = $CONFIG['waba'][$wabaKey]['label'];
        $deviceId   = $CONFIG['waba'][$wabaKey]['device_id'];
        $metaAccId  = $CONFIG['waba'][$wabaKey]['meta_account_id'];

        // Skip kalau template gagal dibuat
        if (!isset($templateIds[$suffix])) {
            log_warn("Skip broadcast $bcName — template tidak tersedia");
            continue;
        }

        // Hitung schedule: base + (idx * 1 menit)
        $bcSchedule = clone $scheduleBase;
        $bcSchedule->modify("+{$idx} minutes");
        $bcScheduleStr = $bcSchedule->format('Y-m-d H:i:s');

        if (!$dryRun) {
            try {
                // Hitung jumlah kontak dulu (untuk stat_total)
                $stmtCount = $pdo->prepare('
                    SELECT COUNT(*) as cnt FROM stores
                    WHERE category_id = :cat_id
                      AND business_id = :biz_id
                      AND phone IS NOT NULL AND phone != ""
                      AND (waba_blocked = 0 OR waba_blocked IS NULL)
                ');
                $stmtCount->execute([':cat_id' => $categoryId, ':biz_id' => $CONFIG['business_id']]);
                $contactCount = (int)$stmtCount->fetchColumn();

                // Insert header broadcast
                $blashId = insertBroadcast($pdo, [
                    'business_id'     => $CONFIG['business_id'],
                    'merchant_id'     => $CONFIG['merchant_id'],
                    'category_id'     => $categoryId,
                    'template_id'     => $templateIds[$suffix],
                    'name'            => $bcName,
                    'device_id'       => $deviceId,
                    'meta_account_id' => $metaAccId,
                    'schedule'        => $bcScheduleStr,
                    'stat_total'      => $contactCount,
                    'file'            => $videoRelPath,
                    'metadata'        => $metadata,
                ]);

                // Populate detail kontak penerima
                $inserted = populateDetails($pdo, $blashId, $deviceId, $categoryId, $CONFIG['business_id']);

                // Update stat_total dengan jumlah aktual yang di-insert
                if ($inserted !== $contactCount) {
                    $pdo->prepare('UPDATE blash_whatsapps SET stat_total = :total WHERE id = :id')
                        ->execute([':total' => $inserted, ':id' => $blashId]);
                }

                $broadcastCount++;
                $totalKontak += $inserted;
                log_ok("$bcName → $label | $bcScheduleStr | $inserted kontak");

            } catch (Exception $e) {
                log_error("$bcName → GAGAL: " . $e->getMessage());
            }
        } else {
            // Dry run: hitung kontak tanpa insert
            try {
                $stmtCount = $pdo->prepare('
                    SELECT COUNT(*) FROM stores
                    WHERE category_id = :cat_id
                      AND business_id = :biz_id
                      AND phone IS NOT NULL AND phone != ""
                      AND (waba_blocked = 0 OR waba_blocked IS NULL)
                ');
                $stmtCount->execute([':cat_id' => $categoryId, ':biz_id' => $CONFIG['business_id']]);
                $contactCount = (int)$stmtCount->fetchColumn();
            } catch (Exception $e) {
                $contactCount = '?';
            }

            $broadcastCount++;
            $totalKontak += is_int($contactCount) ? $contactCount : 0;
            log_info("[DRY] $bcName → $label | $bcScheduleStr | ~$contactCount kontak");
        }
    }

    // --- Summary ---
    echo "\n";
    echo "╔══════════════════════════════════════════════════════╗\n";
    echo "║  SUMMARY                                            ║\n";
    echo "╚══════════════════════════════════════════════════════╝\n";
    log_info("Campaign       : $campaign");
    log_info("Template dibuat: $templateCount / 15");
    log_info("Broadcast dibuat: $broadcastCount / 15");
    log_info("Total kontak   : " . number_format($totalKontak));
    log_info("Jadwal pertama : " . $scheduleBase->format('Y-m-d H:i') . " WIB");
    $lastSchedule = clone $scheduleBase;
    $lastSchedule->modify('+14 minutes');
    log_info("Jadwal terakhir: " . $lastSchedule->format('Y-m-d H:i') . " WIB");

    if ($dryRun) {
        echo "\n";
        log_warn("DRY RUN selesai — tidak ada data yang diubah.");
        log_info("Jalankan ulang tanpa --dry-run untuk eksekusi sesungguhnya.");
    } else {
        echo "\n";
        log_ok("Broadcast berhasil dijadwalkan! Scheduler Laravel akan pickup otomatis.");
        log_info("Monitor di:");
        log_info("  WABA1: " . $CONFIG['app_url'] . "/app/waba/broadcast/" . $CONFIG['waba']['waba1']['meta_account_id']);
        log_info("  WABA2: " . $CONFIG['app_url'] . "/app/waba/broadcast/" . $CONFIG['waba']['waba2']['meta_account_id']);
    }
    echo "\n";
}

// ============================================================
// JALANKAN
// ============================================================
main($argc, $argv);
