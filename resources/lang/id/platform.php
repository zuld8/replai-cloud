<?php

return [
    // ===== WHATSAPP DEVICE =====
    'whatsapp' => [
        // Section Titles
        'device_info'                   => 'Informasi Perangkat',
        'ai_config'                     => 'Konfigurasi AI Agent',
        'schedule_limits'               => 'Jadwal & Batasan',
        
        // Device Information
        'device_name'                   => 'Nama Perangkat',
        'device_name_placeholder'       => 'Contoh: Perangkat Admin',
        'device_name_hint'              => 'Nama untuk mengidentifikasi perangkat WhatsApp ini',
        'wa_number'                     => 'No. WhatsApp',
        'wa_number_placeholder'         => '+62 xxx xxx xxx',
        'wa_number_hint'                => 'Nomor WhatsApp yang akan terhubung dengan sistem (format: +62)',
        'team_member'                   => 'Team Member',
        'team_member_hint'              => 'Pilih anggota tim yang akan mengelola perangkat ini',
        'team_member_placeholder'       => 'Pilih team member...',
        
        // Device Settings
        'device_notification'           => 'Notifikasi Perangkat',
        'device_notification_hint'      => 'Nonaktifkan jika tidak ingin menerima notifikasi di perangkat Anda',
        'save_chat_history'             => 'Simpan Riwayat Pesan',
        'save_chat_history_hint'        => 'Riwayat akan disimpan otomatis tanpa reset harian',
        'auto_read_before_reply'        => 'Baca chat dulu sebelum ChatBot membalas',
        'auto_read_before_reply_hint'   => 'Aktifkan jika ingin membaca chat terlebih dahulu sebelum AI membalas otomatis. Notifikasi ke HP akan dinonaktifkan.',
        'webhook_url'                   => 'WebHook URL (Opsional)',
        'webhook_url_placeholder'       => 'https://example.com/webhook',
        'webhook_url_hint'              => 'URL webhook untuk menerima notifikasi pesan masuk (opsional)',
        
        // AI Configuration
        'chatbot_method'                => 'Metode Chatbot',
        'chatbot_method_hint'           => 'Pilih metode balasan otomatis yang digunakan',
        'method_all'                    => 'Semua (Manual + AI)',
        'method_chatbot'                => 'Chatbot (Manual)',
        'method_ai'                     => 'AI (Fine Tunnel)',
        'ai_training'                   => 'AI Training',
        'ai_training_hint'              => 'Pilih data AI training untuk perangkat ini',
        'ai_training_full_hint'         => 'Pilih dataset training AI untuk meningkatkan akurasi jawaban',
        'ai_training_placeholder'       => 'Pilih AI Training...',
        'select_ai_training'            => 'Pilih AI Training',
        'choose_ai_training'            => 'Pilih AI Training',
        'auto_reply_option'             => 'Chatbot Aktif di',
        'auto_reply_option_hint'        => 'Tentukan di mana chatbot akan aktif membalas pesan',
        'reply_all'                     => 'Semua (Personal & Group)',
        'reply_personal'                => 'Personal',
        'reply_group'                   => 'Group',
        
        // Schedule & Limits
        'inactive_certain_day'          => 'Chatbot Nonaktif di Hari Tertentu',
        'inactive_certain_day_hint'     => 'Aktifkan jika ingin chatbot nonaktif di hari tertentu',
        'select_days'                   => 'Pilih Hari',
        'select_days_placeholder'       => 'Pilih hari...',
        'day_monday'                    => 'Senin',
        'day_tuesday'                   => 'Selasa',
        'day_wednesday'                 => 'Rabu',
        'day_thursday'                  => 'Kamis',
        'day_friday'                    => 'Jumat',
        'day_saturday'                  => 'Sabtu',
        'day_sunday'                    => 'Minggu',
        
        'inactive_certain_time'         => 'Chatbot Nonaktif di Jam Tertentu',
        'inactive_certain_time_hint'    => 'Aktifkan jika ingin chatbot nonaktif di jam tertentu',
        'start_time'                    => 'Jam Mulai Nonaktif',
        'start_time_hint'               => 'Waktu mulai chatbot nonaktif',
        'end_time'                      => 'Jam Selesai Nonaktif',
        'end_time_hint'                 => 'Waktu selesai chatbot nonaktif',
        
        'daily_broadcast_limit'         => 'Batasan Harian Broadcast',
        'daily_broadcast_limit_hint'    => 'Aktifkan jika chatbot memiliki batasan pengiriman pesan per hari',
        'enter_daily_limit'             => 'Masukkan Batas Harian',
        'daily_limit_placeholder'       => 'Contoh: 100',
        'daily_limit_suffix'            => 'pesan/hari',
        'daily_limit_hint'              => 'Jumlah maksimal pesan yang dapat dikirim per hari',
        
        // Actions
        'save_device'                   => 'Simpan Perangkat',
        'update_device'                 => 'Update Perangkat',
        'cancel'                        => 'Batalkan',
        'required_fields'               => 'Field dengan tanda * wajib diisi',
        
        // Messages
        'device_created'                => 'Perangkat WhatsApp berhasil ditambahkan',
        'device_updated'                => 'Perangkat WhatsApp berhasil diupdate',
        'device_deleted'                => 'Perangkat WhatsApp berhasil dihapus',
        
        // List/Index Page
        'add_connection'                => 'Tambah Koneksi WhatsApp',
        'total_device'                  => 'Total Device',
        'not_connected'                 => 'Tidak Terkoneksi',
        'device_connected'              => 'Device Terkoneksi',
        'connection_list'               => 'Daftar Koneksi WhatsApp',
        'broadcast_sent_today'          => 'Pengiriman Broadcast Hari Ini',
        'daily_broadcast_limit_label'   => 'Limit Broadcast Harian',
        'device_name_label'             => 'Nama Device',
        'phone_number'                  => 'Nomor Telepon',
        
        // Actions
        'scan_qr'                       => 'Scan QR',
        'copy_id'                       => 'Salin ID',
        'settings'                      => 'Pengaturan',
        'edit_device'                   => 'Edit Device',
        'delete_device'                 => 'Hapus Device',
        'copied_device_id'              => 'ID Device berhasil disalin',
        'search_device'                 => 'Cari device...',
        
        // Status
        'status_active'                 => 'Aktif',
        'status_inactive'               => 'Tidak Aktif',
    ],
    // ===== TELEGRAM =====
    'telegram' => [
        // Section Titles
        'device_info'                   => 'Informasi Perangkat Telegram',
        'ai_config'                     => 'Konfigurasi AI Agent',
        'schedule_limits'               => 'Jadwal & Batasan',
        'integrated_telegram_list'      => 'Daftar Telegram Ter-integrasi',
        'add_telegram'                   => 'Tambah Telegram',
        'edit_telegram'                  => 'Edit Telegram',
        
        // Device Information
        'device_name'                   => 'Nama Perangkat',
        'device_name_placeholder'       => 'Contoh: Bot Customer Service',
        'device_name_hint'              => 'Nama untuk mengidentifikasi bot Telegram ini',
        'bot_token'                     => 'Bot Token',
        'bot_token_placeholder'         => 'Masukkan bot token dari @BotFather',
        'bot_token_hint'                => 'Token bot yang didapat dari @BotFather di Telegram',
        'team_member'                   => 'Team Member',
        'team_member_hint'              => 'Pilih anggota tim yang akan mengelola bot ini',
        'team_member_placeholder'       => 'Pilih team member...',
        
        // AI Configuration
        'auto_reply_method'             => 'Metode AutoReply',
        'auto_reply_method_hint'        => 'Pilih metode balasan otomatis yang digunakan',
        'method_all'                    => 'Semua (Manual + AI)',
        'method_chatbot'                => 'ChatBot (Manual)',
        'method_ai'                     => 'AI (Fine Tunnel)',
        'ai_training'                   => 'AI Training',
        'ai_training_hint'              => 'Pilih data AI training untuk bot ini',
        'ai_training_placeholder'       => 'Pilih AI Training...',
        'choose_ai_training'            => 'Pilih AI Training',
        
        // Status & Options
        'status'                        => 'Status',
        'status_hint'                   => 'Status aktif/nonaktif bot Telegram',
        'status_active'                 => 'Aktif',
        'status_inactive'               => 'Tidak Aktif',
        'auto_reply_option'             => 'Bot Aktif di',
        'auto_reply_option_hint'        => 'Tentukan di mana bot akan aktif membalas pesan',
        'reply_all'                     => 'Semua (Personal & Group)',
        'reply_personal'                => 'Personal',
        'reply_group'                   => 'Group',
        
        // Schedule & Limits
        'inactive_certain_day'          => 'Bot Nonaktif di Hari Tertentu',
        'inactive_certain_day_hint'     => 'Aktifkan jika ingin bot nonaktif di hari tertentu',
        'inactive_certain_day_no'       => 'Tidak, Aktif Setiap Hari',
        'inactive_certain_day_yes'      => 'Ya, Nonaktif di Hari Tertentu',
        'select_days'                   => 'Pilih Hari',
        'select_days_placeholder'       => 'Pilih hari...',
        'day_monday'                    => 'Senin',
        'day_tuesday'                   => 'Selasa',
        'day_wednesday'                 => 'Rabu',
        'day_thursday'                  => 'Kamis',
        'day_friday'                    => 'Jumat',
        'day_saturday'                  => 'Sabtu',
        'day_sunday'                    => 'Minggu',
        
        'inactive_certain_time'         => 'Bot Nonaktif di Jam Tertentu',
        'inactive_certain_time_hint'    => 'Aktifkan jika ingin bot nonaktif di jam tertentu',
        'inactive_certain_time_no'      => 'Tidak, Aktif 24 Jam',
        'inactive_certain_time_yes'     => 'Ya, Nonaktif di Jam Tertentu',
        'start_time'                    => 'Jam Mulai Nonaktif',
        'start_time_hint'               => 'Waktu mulai bot nonaktif',
        'end_time'                      => 'Jam Selesai Nonaktif',
        'end_time_hint'                 => 'Waktu selesai bot nonaktif',
        
        'daily_limit'                   => 'Batasan Harian',
        'daily_limit_hint'              => 'Aktifkan jika bot memiliki batasan pengiriman pesan per hari',
        'daily_limit_no'                => 'Tidak Ada Batasan',
        'daily_limit_yes'               => 'Ada Batasan Harian',
        'enter_daily_limit'             => 'Masukkan Batas Harian',
        'daily_limit_placeholder'       => 'Contoh: 1000',
        'daily_limit_suffix'            => 'pesan/hari',
        'daily_limit_hint_input'        => 'Jumlah maksimal pesan yang dapat dikirim per hari',
        
        // Actions
        'back_to_list'                  => 'Kembali Ke Halaman Telegram',
        'add_device'                    => 'Tambah Perangkat',
        'save_device'                   => 'Simpan Perangkat',
        'update_device'                 => 'Update Perangkat',
        'cancel'                        => 'Batalkan',
        'required_fields'               => 'Field dengan tanda * wajib diisi',
        
        // Messages
        'device_created'                => 'Bot Telegram berhasil ditambahkan',
        'device_updated'                => 'Bot Telegram berhasil diupdate',
        'device_deleted'                => 'Bot Telegram berhasil dihapus',
        
        // List/Index Page
        'add_connection'                => 'Tambah Koneksi Telegram',
        'total_bot'                     => 'Total Telegram',
        'not_connected'                 => 'Tidak Terkoneksi',
        'bot_connected'                 => 'Telegram Terkoneksi',
        'connection_list'               => 'Daftar Koneksi Telegram',
        'bot_name'                      => 'Nama Bot',
        'broadcast_sent_today'          => 'Pengiriman Broadcast Hari Ini',
        'daily_broadcast_limit_label'   => 'Limit Broadcast Harian',
        
        // Actions List
        'copy_id'                       => 'Salin ID',
        'edit_bot'                      => 'Edit Bot',
        'delete_bot'                    => 'Hapus Bot',
        'copied_bot_id'                 => 'ID Bot berhasil disalin',
        'search_bot'                    => 'Cari bot telegram...',
    ],
    'facebook' => [
        'add_account'                   => 'Tambahkan Akun',
        'account_list'                  => 'Daftar Akun Facebook',
        'account_connected'             => 'Akun Facebook berhasil terhubung.',
        'login_failed'                  => 'Gagal login Facebook: ',
    ],
    'instagram' => [
        'add_account'                   => 'Tambahkan Akun',
        'account_list'                  => 'Daftar Akun Instagram',
        'account_connected'             => 'Akun Instagram berhasil terhubung.',
        'login_failed'                  => 'Gagal login Instagram: ',
    ],

];