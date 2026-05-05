<?php

return [
    'wa' => [
        // Index/List Page
        'create_schedule_button' => 'Buat Jadwal Broadcast',
        'wa_group' => 'Wa Group',
        'page_title' => 'Daftar Broadcast WhatsApp',
        
        // Card Headers
        'broadcast_info' => 'Informasi Broadcast',
        'target_template' => 'Target & Template',
        
        // Form Labels
        'name' => 'Judul Broadcast',
        'schedule' => 'Jadwal Kirim',
        'template' => 'Template Pesan',
        'devices' => 'Perangkat Device',
        'device_option' => 'Opsi Penggunaan Device',
        'delay' => 'Jeda Antar Pengiriman',
        'stop_sending' => 'Stop Sending After',
        'rest_sending' => 'Jeda Istirahat',
        'category' => 'Kategori Bisnis',
        'whatsapp_group' => 'Whatsapp Group',
        'location_target' => 'Lokasi Target',
        
        // Placeholders
        'name_placeholder' => 'Contoh: Promo Akhir Tahun 2024',
        'choose_group' => 'Pilih Group whatsapp',
        'choose_device' => 'Pilih Device WhatsApp',
        
        // Device Options
        'device_sequence' => 'Single Device (Berurutan)',
        'device_spin' => 'AI Choose (Spin)',
        'device_random' => 'Random (Acak)',
        
        // Units
        'seconds' => 'detik',
        'numbers' => 'nomor',
        
        // Helper Texts
        'name_help' => 'Nama kampanye broadcast untuk identifikasi',
        'schedule_help' => 'Jadwal pengiriman pesan broadcast',
        'template_help' => 'Template pesan yang akan dikirim',
        'devices_help' => 'Pilih device WhatsApp untuk mengirim broadcast',
        'device_option_help' => 'Metode pemilihan device untuk pengiriman',
        'delay_help' => 'Rekomendasi: 30-300 detik. Semakin kecil, semakin berisiko terblokir',
        'stop_sending_help' => 'Berhenti mengirim setelah berapa nomor',
        'rest_sending_help' => 'Jeda sebelum kembali mengirim pesan',
        'category_help' => 'Filter target berdasarkan kategori bisnis',
        'whatsapp_group_help' => 'Target kontak dari grup WhatsApp tertentu',
        'province_help' => 'Filter target berdasarkan provinsi',
        'city_help' => 'Filter target berdasarkan kota/kabupaten',
        'district_help' => 'Filter target berdasarkan kecamatan',
        
        // Badges & Labels
        'optional' => 'Opsional',
        'required_field' => 'Wajib diisi',
        
        // Alert & Tips
        'safe_sending_tips' => 'Tips Pengiriman Aman:',
        'tip_delay' => 'Gunakan delay minimal 30 detik untuk menghindari pemblokiran',
        'tip_batch' => 'Batasi pengiriman maksimal 50-100 pesan per batch',
        'tip_rest' => 'Berikan jeda istirahat yang cukup antar batch',
        'tip_multiple' => 'Gunakan multiple device untuk load balancing',
    ],
    
    'email' => [
        // Form Fields
        'category' => 'Kategori Bisnis',
        'template' => 'Template Email',
        'name' => 'Judul Broadcast',
        'schedule' => 'Jadwal Kirim',
        
        // Placeholders
        'name_placeholder' => 'Contoh: Newsletter Bulanan Oktober 2024',
        
        // Helper Texts
        'category_help' => 'Filter target penerima berdasarkan kategori bisnis',
        'template_help' => 'Template email yang akan dikirimkan ke penerima',
        'name_help' => 'Nama kampanye broadcast untuk identifikasi',
        'schedule_help' => 'Jadwal pengiriman email broadcast',
        
        // Badges & Labels
        'optional' => 'Opsional',
        'required_field' => 'Wajib diisi',
        
        // Alert & Tips
        'email_sending_tips' => 'Tips Pengiriman Email:',
        'tip_test_template' => 'Pastikan template email sudah diuji sebelum broadcast',
        'tip_optimal_time' => 'Pilih waktu pengiriman yang optimal untuk engagement maksimal',
        'tip_use_category' => 'Gunakan kategori untuk targeting yang lebih spesifik',
        'tip_check_spam' => 'Periksa kembali konten untuk menghindari masuk spam',
    ],
    
    'upselling' => [
        // List/Index Page
        'page_title' => 'Daftar Upselling Campaign',
        'create_campaign_button' => 'Buat Campaign Baru',
        'refresh_button' => 'Refresh',
        
        // Table Headers
        'campaign_info' => 'Campaign Info',
        'schedule_frequency' => 'Jadwal & Frekuensi',
        'target_category' => 'Target & Kategori',
        'method_template' => 'Metode & Template',
        'status' => 'Status',
        'action' => 'Aksi',
        
        // Filter Options
        'all_status' => 'Semua Status',
        'status_active' => 'Aktif',
        'status_inactive' => 'Tidak Aktif',
        'status_scheduled' => 'Terjadwal',
        'status_completed' => 'Selesai',
        'all_frequency' => 'Semua Frekuensi',
        'frequency_once' => 'Sekali',
        'frequency_daily' => 'Harian',
        'frequency_monthly' => 'Bulanan',
        'frequency_yearly' => 'Tahunan',
        
        // DataTable Language
        'search_placeholder' => 'Cari campaign...',
        'length_menu' => 'Tampilkan _MENU_ campaign per halaman',
        'info' => 'Menampilkan _START_ sampai _END_ dari _TOTAL_ campaign',
        'info_empty' => 'Tidak ada campaign',
        'info_filtered' => '(difilter dari _MAX_ total campaign)',
        'paginate_first' => 'Pertama',
        'paginate_last' => 'Terakhir',
        'paginate_next' => 'Selanjutnya',
        'paginate_previous' => 'Sebelumnya',
        
        // DataTable Content
        'devices_count' => 'perangkat',
        'delay_label' => 'Delay',
        'once_send' => 'Pengiriman satu kali',
        'date_prefix' => 'Tanggal',
        'every_month' => 'setiap bulan',
        'every_year' => 'setiap tahun',
        'ongoing' => 'Berlanjut',
        'all_categories' => 'Semua kategori',
        'labels_count' => 'label',
        'template_label' => 'Template',
        'ai_generated' => 'AI Generated',
        
        // Day Short Labels
        'day_monday_short' => 'Sen',
        'day_tuesday_short' => 'Sel',
        'day_wednesday_short' => 'Rab',
        'day_thursday_short' => 'Kam',
        'day_friday_short' => 'Jum',
        'day_saturday_short' => 'Sab',
        'day_sunday_short' => 'Min',
        
        // Month Short Labels
        'month_jan' => 'Jan',
        'month_feb' => 'Feb',
        'month_mar' => 'Mar',
        'month_apr' => 'Apr',
        'month_may' => 'Mei',
        'month_jun' => 'Jun',
        'month_jul' => 'Jul',
        'month_aug' => 'Agu',
        'month_sep' => 'Sep',
        'month_oct' => 'Okt',
        'month_nov' => 'Nov',
        'month_dec' => 'Des',
        
        // Action Button Tooltips
        'btn_view_detail' => 'Lihat Detail',
        'btn_edit_campaign' => 'Edit Campaign',
        'btn_duplicate_campaign' => 'Duplikasi Campaign',
        'btn_delete_campaign' => 'Hapus Campaign',
        
        // Confirmation Messages
        'confirm_activate' => 'Apakah Anda yakin ingin mengaktifkan campaign ini?',
        'confirm_deactivate' => 'Apakah Anda yakin ingin menonaktifkan campaign ini?',
        'confirm_delete' => 'Apakah Anda yakin ingin menghapus campaign ini? Aksi ini tidak dapat dibatalkan.',
        
        // Success Messages
        'success_title' => 'Berhasil',
        'success_delete' => 'Campaign berhasil dihapus',
        
        // Error Messages
        'error_title' => 'Error',
        'error_status_change' => 'Terjadi kesalahan saat mengubah status campaign',
        'error_delete' => 'Terjadi kesalahan saat menghapus campaign',
        
        // Back Button
        'back_to_campaign' => 'Kembali Ke Upselling Campaign',
        
        // Card Headers (Form)
        'basic_info' => 'Informasi Dasar Campaign',
        'schedule_config' => 'Opsi Penjadwalan Pengiriman & Konfigurasi Pesan',
        
        // Form Fields
        'campaign_title' => 'Judul Upselling Campaign',
        'delay' => 'Delay Antar Pengiriman',
        'devices' => 'Perangkat Device',
        'device_option' => 'Opsi Penggunaan Device',
        'contact_category' => 'Kategori Kontak',
        'contact_labels' => 'Label Kontak',
        'schedule_frequency' => 'Frekuensi Pengiriman',
        'select_days' => 'Pilih Hari',
        'date_in_month' => 'Tanggal dalam Bulan',
        'specific_date' => 'Tanggal Spesifik',
        'sending_time' => 'Waktu Pengiriman',
        'start_date' => 'Tanggal Mulai',
        'end_date' => 'Tanggal Berakhir',
        'broadcast_method' => 'Metode Yang Digunakan',
        'ai_prompt' => 'Prompt AI',
        'template_message' => 'Template Pesan',
        
        // Placeholders
        'name_placeholder' => 'Contoh: Campaign Promo Akhir Tahun',
        'ai_prompt_placeholder' => 'Contoh: Buatkan pesan upselling untuk produk baru dengan tone yang friendly dan menarik...',
        'select_category' => 'Pilih kategori...',
        'select_template' => 'Pilih template...',
        'select_device' => 'Pilih perangkat...',
        'select_day' => 'Pilih hari...',
        
        // Helper Texts
        'name_help' => 'Nama kampanye upselling untuk identifikasi internal',
        'delay_help' => 'Jeda waktu antar pengiriman pesan',
        'devices_help' => 'Pilih satu atau lebih perangkat untuk mengirim pesan',
        'device_option_help' => 'Metode pemilihan device untuk pengiriman pesan',
        'category_help' => 'Filter kontak berdasarkan kategori tertentu',
        'labels_help' => 'Pilih label kontak yang akan menerima pesan (bisa multiple)',
        'frequency_help' => 'Tentukan seberapa sering pesan akan dikirim',
        'days_help' => 'Pilih hari-hari untuk pengiriman',
        'date_help' => 'Pilih tanggal dalam bulan untuk pengiriman',
        'yearly_help' => 'Pilih bulan dan tanggal untuk pengiriman tahunan',
        'time_help' => 'Jam pengiriman pesan',
        'start_date_help' => 'Tanggal mulai kampanye',
        'end_date_help' => 'Kosongkan jika tidak ada batas waktu',
        'method_help' => 'Pilih metode pembuatan pesan campaign',
        'ai_prompt_help' => 'AI akan menggunakan prompt ini untuk membuat pesan yang disesuaikan dengan setiap kontak',
        'template_help' => 'Pilih template pesan yang sudah dibuat sebelumnya',
        
        // Frequency Options
        'freq_once' => 'Kirim Sekali',
        'freq_daily' => 'Harian',
        'freq_monthly' => 'Bulanan',
        'freq_yearly' => 'Tahunan',
        
        // Days
        'monday' => 'Senin',
        'tuesday' => 'Selasa',
        'wednesday' => 'Rabu',
        'thursday' => 'Kamis',
        'friday' => 'Jumat',
        'saturday' => 'Sabtu',
        'sunday' => 'Minggu',
        'last_day' => 'Hari Terakhir',
        
        // Months
        'january' => 'Januari',
        'february' => 'Februari',
        'march' => 'Maret',
        'april' => 'April',
        'may' => 'Mei',
        'june' => 'Juni',
        'july' => 'Juli',
        'august' => 'Agustus',
        'september' => 'September',
        'october' => 'Oktober',
        'november' => 'November',
        'december' => 'Desember',
        
        // Broadcast Methods
        'method_template_option' => 'Gunakan Template',
        'method_ai_option' => 'Gunakan AI Prompt',
        
        // Device Options
        'device_sequence' => 'Single Device (Berurutan)',
        'device_spin' => 'AI Choose (Otomatis Pilih)',
        'device_random' => 'Random (Acak)',
        
        // Units
        'seconds' => 'detik',
        
        // Badges & Labels
        'optional' => 'Opsional',
        'required_field' => 'Wajib diisi',
        
        // Buttons
        'create_campaign' => 'Buat Campaign',
        'update_campaign' => 'Update Campaign',
        
        // Alert & Tips
        'campaign_tips' => 'Tips Upselling Campaign:',
        'tip_ai_prompt' => 'Gunakan AI prompt untuk pesan yang lebih personal dan dinamis',
        'tip_optimal_time' => 'Pilih waktu pengiriman yang tepat untuk engagement maksimal',
        'tip_use_labels' => 'Manfaatkan label untuk targeting yang lebih spesifik',
        'tip_frequency' => 'Gunakan frekuensi harian/bulanan untuk follow-up otomatis',
    ]
];