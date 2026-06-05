# Replai.id System Documentation
> Untuk AI Agent — Baca ini sebelum melakukan apapun di sistem

---

## 1. Overview Sistem

**Replai.id** (`chat.replai.id`) adalah platform CRM WhatsApp multi-tenant berbasis Laravel.

- **Stack:** Laravel (PHP), MySQL (`whatsmail`), Redis (queue), Laravel Horizon (worker)
- **Server:** `147.93.159.3` (root), SSH port 22
- **App dir:** `/var/www/html/chat.replai.id`
- **DB:** `whatsmail` @ `127.0.0.1:3306`
- **Queue:** Redis @ `127.0.0.1:6379`, connection name `redis`
- **Timezone DB:** Schedule field = WIB (UTC+7), created_at/updated_at = UTC

---

## 2. Hierarki Entitas (Multi-Tenant)

```
businesses
  └── merchants          (1 business bisa punya banyak merchant)
        └── users         (agent/admin per merchant)
        └── meta_accounts (WABA = WhatsApp Business API account)
              └── whatsapp_key_accounts  (device/nomor pengirim)
        └── categories    (grup kontak)
              └── stores  (kontak individual)
        └── blash_whatsapps (broadcast campaign)
              └── blash_details (per-kontak pengiriman)
```

### Tabel Kunci & Relasinya

| Tabel | Fungsi | Key Fields |
|---|---|---|
| `businesses` | Top-level tenant | `id`, `name` |
| `merchants` | Sub-tenant/workspace | `id`, `business_id`, `name` |
| `users` | Agent/admin | `id`, `merchant_id`, `business_id`, `role_id` |
| `roles` | Spatie roles | `id`, `name`, `guard_name` |
| `model_has_roles` | Pivot user↔role | `model_id`, `role_id` |
| `meta_accounts` | WABA account Meta | `id`, `phone`, `phone_number_id`, `business_id`, `access_token` |
| `whatsapp_key_accounts` | Device/nomor WA | `id`, `phone`, `meta_account_id`, `business_id`, `status` |
| `waba_agents` | Pivot agent↔device | `id`, `waba_id` (=waka.id), `user_id` |
| `categories` | Grup kontak | `id`, `name`, `merchant_id`, `business_id` |
| `stores` | Kontak/customer | `id`, `phone`, `name`, `category_id`, `business_id`, `waba_blocked` |
| `blash_whatsapps` | Broadcast header | `id`, `name`, `devices`, `meta_account_id`, `category_id`, `status`, `schedule` |
| `blash_details` | Per-kontak kirim | `id`, `blash_whatsapp_id`, `store_id`, `phone`, `device_id`, `wamid`, `delivery_status` |
| `blash_details_archive` | Archive detail lama | sama dengan blash_details |
| `history_chats` | Thread percakapan | `id`, `device_id`, `whatsapp_waba_id`, `from_number`, `store_id`, `business_id` |
| `history_chat_details` | Pesan individual | `id`, `history_chat_id`, `message`, `from` |
| `message_templates` | Template WA | `id`, `meta_account_id`, `name`, `status` |

---

## 3. Alur Broadcast (Kirim Pesan Massal)

### 3.1 Lifecycle Normal (Dibuat dari UI)

```
User buat broadcast di UI
  → blash_whatsapps dibuat (status: pending)
  → blash_details di-populate otomatis dari stores (by category_id)
    → device_id di blash_details = blash_whatsapps.devices ✅
  → Scheduler (tiap menit) cek: schedule <= NOW() && status='pending'
  → Dispatch SendPromotionWhatsappBatchJob per chunk 100
  → status → 'processing'
  → Job kirim via Meta API → dapat wamid
  → delivery_status: queued → dispatched → processing → sent → delivered/read
  → Setelah semua selesai: status → 'success' atau 'partial_success' atau 'failed'
```

### 3.2 Field Penting di `blash_whatsapps`

| Field | Keterangan |
|---|---|
| `devices` | UUID device (whatsapp_key_accounts.id) yang dipakai kirim |
| `meta_account_id` | UUID WABA (meta_accounts.id) |
| `category_id` | Sumber kontak (dari categories.id) |
| `status` | `pending` → `processing` → `success`/`partial_success`/`failed` |
| `stat_total` | Total kontak |
| `stat_sent` | Terkirim (update async dari callback) |
| `stat_failed` | Gagal |
| `schedule` | Waktu kirim **dalam WIB (UTC+7)** |
| `waba` | `'yes'` = pakai WABA (Meta API), `'no'` = pakai device biasa |

### 3.3 Field Penting di `blash_details`

| Field | Keterangan |
|---|---|
| `blash_whatsapp_id` | FK ke broadcast header |
| `store_id` | FK ke stores (kontak) |
| `phone` | Nomor tujuan |
| `device_id` | **HARUS sama dengan blash_whatsapps.devices** |
| `wamid` | WhatsApp Message ID — jika NULL = belum terkirim |
| `delivery_status` | `NULL`→`queued`→`dispatched`→`processing`→`sent`→`delivered`/`read`/`failed` |
| `sending_status` | `'no'` = belum, `'yes'` = sudah di-dispatch |
| `reports` | Error message jika gagal (JSON string) |

### 3.4 Error Codes Umum

| Code | Artinya | Bisa Retry? |
|---|---|---|
| `131026` | Nomor tidak terdaftar di WhatsApp | ❌ Tidak |
| `131042` | Business eligibility blocked (billing) | ❌ Perlu Meta Support |
| `BATCH_INCOMPLETE` | Batch terputus/rate limit | ✅ Ya, reset dan retry |
| `133016` | Rate limit re-register | ⏳ Tunggu |

---

## 4. WABA & Device Routing — KRITIS!

### 4.1 Hierarki WABA

```
meta_accounts (WABA)
  └── whatsapp_key_accounts (device/nomor fisik)
        └── waba_agents (agent yg bisa akses device ini)
```

- 1 `meta_account` = 1 WABA = bisa punya beberapa `whatsapp_key_accounts`
- `blash_whatsapps.devices` = `whatsapp_key_accounts.id` (bukan `meta_account.id`!)
- `blash_whatsapps.meta_account_id` = `meta_accounts.id`

### 4.2 ⚠️ ATURAN KRITIS: Manual Populate blash_details

Jika harus manual insert `blash_details`, **WAJIB** set `device_id`:

```sql
-- ✅ BENAR
INSERT INTO blash_details (id, store_id, blash_whatsapp_id, phone, device_id, sending_status, created_at, updated_at)
SELECT UUID(), s.id, '{bc_id}', s.phone, '{device_id_dari_blash_whatsapps}', 'no', NOW(), NOW()
FROM stores s WHERE s.category_id = '{cat_id}';

-- ❌ SALAH — akan kirim via device random/salah
INSERT INTO blash_details (id, store_id, blash_whatsapp_id, phone, sending_status, created_at, updated_at)
...
```

Cara ambil device_id yang benar:
```sql
SELECT devices FROM blash_whatsapps WHERE id = '{broadcast_id}';
```

### 4.3 Cara Cek Cross-Device (Routing Salah)

```sql
SELECT bw.name, bw.devices as intended, bd.device_id as actual, COUNT(*) cnt
FROM blash_details bd
JOIN blash_whatsapps bw ON bd.blash_whatsapp_id = bw.id
WHERE bd.device_id IS NOT NULL AND bd.device_id != bw.devices
GROUP BY bw.name, bw.devices, bd.device_id;
```

### 4.4 Fix Cross-Device untuk Record Belum Terkirim

```sql
UPDATE blash_details bd
JOIN blash_whatsapps bw ON bd.blash_whatsapp_id = bw.id
SET bd.device_id = bw.devices
WHERE bd.wamid IS NULL AND bd.device_id != bw.devices;
```

---

## 5. Kontak (Stores)

- Tabel kontak utama: **`stores`** (bukan `scrappings`!)
- `scrappings` = tracking job scraping (hanya 13 records, bukan data kontak)
- Kontak dikelompokkan via `category_id` → `categories`
- Filter penting: `waba_blocked = 0` atau `waba_blocked IS NULL`

```sql
-- Ambil kontak dari kategori tertentu
SELECT * FROM stores WHERE category_id = '{cat_id}' AND phone IS NOT NULL;

-- Count kontak per kategori
SELECT c.name, COUNT(s.id) FROM categories c
LEFT JOIN stores s ON s.category_id = c.id
WHERE c.merchant_id = '{mid}' GROUP BY c.id;
```

---

## 6. CRM / Chat Flow

```
Pesan masuk dari WA
  → Webhook → history_chats (thread) + history_chat_details (pesan)
  → history_chats.device_id = whatsapp_key_accounts.id
  → history_chats.whatsapp_waba_id = whatsapp_key_accounts.id
  → history_chats.from_number = nomor customer
  → history_chats.store_id = stores.id (jika matched)

Agent balas
  → history_chat_details.from = 'agent'
  → history_chats.handled_by = users.id
```

### Status Percakapan
- `open` = aktif/belum selesai
- `resolved` = diselesaikan
- `pending` = menunggu
- `block` = diblokir

---

## 7. Akses Agent ke WABA

Tabel `waba_agents` mengontrol agent mana yang bisa akses WABA mana:

```sql
-- Cek agent yg bisa akses WABA tertentu
SELECT u.name, u.email FROM waba_agents wa
JOIN users u ON u.id = wa.user_id
WHERE wa.waba_id = '{whatsapp_key_accounts.id}';

-- Tambah akses agent ke WABA
INSERT INTO waba_agents (id, waba_id, user_id, created_at, updated_at)
VALUES (UUID(), '{waka_id}', '{user_id}', NOW(), NOW());
```

---

## 8. Queue & Horizon

- **Engine:** Laravel Horizon (Redis-based)
- **Prefix Redis:** `whatscrmhubqueues:`
- **Job utama broadcast:** `SendPromotionWhatsappBatchJob`
- **Dispatch format:**
  ```php
  dispatch(new \App\Jobs\SendPromotionWhatsappBatchJob($chunk_ids, $delay, $stop_limit, $rest_limit));
  // $chunk_ids = array of blash_details.id (max 100 per chunk)
  // $delay = delay antar pesan (detik)
  // $stop_limit = jumlah kirim sebelum istirahat
  // $rest_limit = lama istirahat (detik)
  ```
- **Monitor:** `php artisan horizon:status`
- **Restart:** `php artisan horizon:terminate` (auto restart via supervisor)

### Re-dispatch Manual (via Tinker)

```php
// ⚠️ SELALU jalankan sebagai www-data, bukan root!
// sudo -u www-data php artisan tinker

$bc = \App\Models\Blash\BlashWhatsapp::withoutGlobalScopes()->find($bc_id);
$ids = \App\Models\Blash\BlashDetail::where('blash_whatsapp_id', $bc_id)
    ->where('sending_status', 'no')->whereNull('wamid')->pluck('id')->toArray();
foreach (array_chunk($ids, 100) as $chunk) {
    dispatch(new \App\Jobs\SendPromotionWhatsappBatchJob($chunk, 5, 20, 90));
}
$bc->update(['status' => 'processing']);
```

---

## 9. Cron Jobs Aktif

| Schedule | Command | Fungsi |
|---|---|---|
| `* * * * *` | `php artisan schedule:run` | Laravel scheduler (broadcast trigger, dll) |
| `* * * * *` | `chown www-data storage/logs/*.log` | **Fix log permission** (added manual) |
| `*/5 * * * *` | `horizon-health-check.sh` | Auto-restart Horizon jika mati |
| `*/30 * * * *` | `git-sync` | Sync kode ke GitHub |
| `0 3 * * *` | `replai-backup.sh` | Backup harian |
| `0 4 * * *` | `find backups -mtime +7 -delete` | Hapus backup >7 hari |
| `0 0 * * *` | `chown www-data logs/*.log` | Fix permission midnight |

### ⚠️ Masalah Log Permission

Jika menjalankan `php artisan tinker` sebagai **root**, log file akan dimiliki root → queue worker (www-data) tidak bisa tulis → **semua job gagal silently**.

**Ciri-ciri:**
- `blash_details` semua `delivery_status = processing` tapi `wamid = NULL`
- `failed_jobs` berisi error: `"could not be opened in append mode"`

**Fix:**
```bash
chown www-data:www-data /var/www/html/chat.replai.id/storage/logs/*.log
chmod 664 /var/www/html/chat.replai.id/storage/logs/*.log
# Hapus failed jobs lalu re-dispatch
```

**Pencegahan:** Cron `* * * * *` sudah ada untuk auto-fix permission.

---

## 10. Backup

- **Lokasi:** `/root/backups/daily/`
- **Nama file:** `broadcast_backup_YYYYMMDD_HHMMSS.sql.gz`
- **Retention:** 7 hari (auto-delete)
- **Manual backup:**
  ```bash
  mysqldump -h127.0.0.1 -uroot --password=Goldenreog20 whatsmail \
    --where="created_at >= '2026-06-04'" \
    blash_whatsapps blash_details | gzip > /root/backups/daily/manual_backup.sql.gz
  ```

---

## 11. Masalah Umum & Fix

### 11.1 Broadcast stuck "pending" / tidak jalan

```sql
-- Cek status
SELECT name, status, stat_total, schedule FROM blash_whatsapps
WHERE status='pending' AND schedule <= NOW();

-- Cek blash_details ada isinya tidak
SELECT COUNT(*) FROM blash_details WHERE blash_whatsapp_id = '{bc_id}';
```

**Kemungkinan penyebab:**
1. `blash_details` kosong (0 record) → populate manual dari `stores`
2. Log permission error → fix chown
3. Horizon mati → `php artisan horizon:status`

### 11.2 Broadcast "success" tapi stat_total = 0

`blash_details` kosong saat broadcast dibuat. Kategori kontak mungkin kosong atau ada bug saat populate. Fix: populate manual + re-dispatch.

### 11.3 Error 131042 (Business Eligibility)

- Blocked oleh Meta billing engine
- **Tidak bisa fix via API**
- Harus hubungi Meta Business Support langsung
- Terjadi pada akun yang belum verifikasi pembayaran

### 11.4 BATCH_INCOMPLETE

```sql
-- Reset dan retry
UPDATE blash_details SET delivery_status=NULL, sending_status='no', reports=NULL
WHERE blash_whatsapp_id='{bc_id}' AND reports LIKE '%BATCH_INCOMPLETE%';
-- Lalu re-dispatch
```

### 11.5 Role "Bidang Wajib Diisi" di UI

- DB benar (users.role_id + model_has_roles terisi)
- Kemungkinan bug frontend form binding
- Workaround: update via SQL langsung jika urgent

---

## 12. Query Berguna

```sql
-- Status broadcast hari ini
SELECT name, status, stat_total, stat_sent, stat_failed,
    (SELECT COUNT(*) FROM blash_details WHERE blash_whatsapp_id=bw.id AND wamid IS NOT NULL) wamid_cnt
FROM blash_whatsapps bw
WHERE schedule >= CURDATE() ORDER BY schedule;

-- Cek failed jobs terbaru
SELECT LEFT(payload,80) job, LEFT(exception,200) err, failed_at
FROM failed_jobs ORDER BY failed_at DESC LIMIT 5;

-- Cek device semua WABA dalam satu bisnis
SELECT ma.id, ma.phone as waba_phone, wka.id device_id, wka.phone device_phone, wka.status
FROM meta_accounts ma
JOIN whatsapp_key_accounts wka ON wka.meta_account_id = ma.id
WHERE ma.business_id = '{bid}';

-- Cek kontak per kategori
SELECT c.name, COUNT(s.id) contacts
FROM categories c LEFT JOIN stores s ON s.category_id = c.id
WHERE c.merchant_id = '{mid}' GROUP BY c.id ORDER BY contacts DESC;

-- Cek duplikat nomor dalam broadcast
SELECT phone, COUNT(*) cnt FROM blash_details
WHERE blash_whatsapp_id = '{bc_id}'
GROUP BY phone HAVING cnt > 1;
```

---

## 13. Hal yang JANGAN Dilakukan

| ❌ Jangan | ✅ Lakukan sebagai gantinya |
|---|---|
| Insert `blash_details` tanpa `device_id` | Selalu set `device_id = bw.devices` |
| Jalankan `php artisan tinker` sebagai root | `sudo -u www-data php artisan tinker` |
| Dispatch ulang broadcast yang sudah `wamid IS NOT NULL` | Hanya dispatch yang `wamid IS NULL` |
| Delete `blash_details` tanpa backup | Selalu backup dulu |
| Ubah `status = 'success'` yang sudah beres | Biarkan saja |
| Restart Horizon sembarangan | Cek `horizon:status` dulu |

---

## 14. Akun Penting (Contoh Bisnis)

> **Catatan:** Data ini dari sesi debug aktif. Gunakan query untuk cek data terbaru.

### Africa/Darura (Nomor: +6285165814828)
- Device ID: `e6bfd1d5-8b49-4a32-9818-c9dba5079878`
- Meta Account: `95997440-7d90-42d8-9710-db97ef8f9430`
- Business ID: `229ae208-6270-483d-b6f3-14948abdf508`
- Merchant ID: `31920758-a779-4a96-bcfa-4fc909b14d14`
- Kontak: ~72,649 di tabel `stores`
- Kategori: "5K PART 1" s/d "5K PART 14" (~4,990-5,530 kontak per part)

### MinDA (terblokir 131042)
- WABA ID: `959613020508745`

### MinLev (terblokir 131042)
- WABA ID: `1011515394637993`

---

*Dokumen ini dihasilkan dari debugging sesi aktif. Update sesuai perubahan sistem.*
