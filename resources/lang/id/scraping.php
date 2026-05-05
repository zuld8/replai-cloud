<?php

return [
    'maps' => [
        // Page Titles & Navigation
        'page_title' => 'Google Maps Scraping',
        'create_title' => 'Buat Scraping Baru',
        'update_title' => 'Edit Scraping',
        'back_to' => 'Kembali ke Scraping',
        
        // Card Headers
        'scraping_info' => 'Informasi Scraping',
        'location_target' => 'Lokasi Target Scraping',
        
        // Form Labels
        'category' => 'Kategori Bisnis',
        'name' => 'Nama Scraping',
        'schedule' => 'Jadwal Scraping',
        'province' => 'Provinsi',
        'city' => 'Kota/Kabupaten',
        'district' => 'Kecamatan',
        
        // Placeholders
        'name_placeholder' => 'Contoh: Scraping Restoran Jakarta',
        'choose_category' => 'Pilih kategori bisnis',
        
        // Helper Texts
        'category_help' => 'Kategori bisnis yang akan di-scrape dari Google Maps',
        'name_help' => 'Nama untuk identifikasi proses scraping ini',
        'schedule_help' => 'Jadwal kapan proses scraping akan dijalankan',
        'province_help' => 'Filter scraping berdasarkan provinsi tertentu',
        'city_help' => 'Filter scraping berdasarkan kota/kabupaten',
        'district_help' => 'Filter scraping berdasarkan kecamatan spesifik',
        
        // Badges & Labels
        'optional' => 'Opsional',
        'required' => 'Wajib diisi',
        'required_field' => 'Wajib diisi',
        
        // Tips & Alerts
        'tips_title' => 'Tips Scraping Google Maps:',
        'tip_location' => 'Semakin spesifik lokasi yang dipilih, hasil scraping akan lebih fokus',
        'tip_category' => 'Pilih kategori yang sesuai dengan target bisnis Anda',
        'tip_schedule' => 'Jadwalkan scraping pada waktu dengan traffic rendah untuk hasil optimal',
        'tip_auto' => 'Proses scraping akan berjalan otomatis sesuai jadwal yang ditentukan',
        
        // Buttons
        'btn_create' => 'Mulai Scraping',
        'btn_update' => 'Simpan Perubahan',
        
        // Status
        'status_pending' => 'Menunggu',
        'status_processing' => 'Sedang Proses',
        'status_completed' => 'Selesai',
        'status_failed' => 'Gagal',
        
        // Messages
        'success_create' => 'Scraping berhasil dijadwalkan',
        'success_update' => 'Scraping berhasil diperbarui',
        'success_delete' => 'Scraping berhasil dihapus',
        'error_create' => 'Gagal membuat scraping',
        'error_update' => 'Gagal memperbarui scraping',
        'error_delete' => 'Gagal menghapus scraping',
        
        // Meta
        'title' => 'Scraping Google Maps',
        'description' => 'Ekstrak data bisnis dari Google Maps berdasarkan kategori dan lokasi',
    ],
    
    'contact' => [
        // Page Titles & Navigation
        'page_title' => 'Scraping Kontak WhatsApp',
        'create_title' => 'Tambah Scraping Kontak',
        'update_title' => 'Edit Scraping Kontak',
        'back_to' => 'Kembali ke Scraping',
        'list_title' => 'Daftar Data Scrap Kontak Whatsapp',

        // Card Headers
        'scraping_info' => 'Konfigurasi Scraping',
        
        // Form Labels
        'devices' => 'Perangkat Device',
        'category' => 'Kategori',
        'name' => 'Nama Scraping',
        'schedule' => 'Jadwal Scraping',
        
        // Placeholders
        'name_placeholder' => 'Contoh: Scraping Kontak Grup Marketing',
        'choose_devices' => 'Pilih perangkat...',
        'choose_category' => 'Pilih kategori',
        
        // Helper Texts
        'devices_help' => 'Pilih device WhatsApp yang akan digunakan untuk scraping',
        'category_help' => 'Kategori untuk mengklasifikasikan kontak hasil scraping',
        'name_help' => 'Nama untuk identifikasi proses scraping ini',
        'schedule_help' => 'Jadwal kapan proses scraping kontak akan dijalankan',
        
        // Labels
        'required' => 'Wajib diisi',
        'required_field' => 'Wajib diisi',
        
        // Tips & Alerts
        'tips_title' => 'Tips Scraping Kontak WhatsApp:',
        'tip_device' => 'Gunakan multiple device untuk scraping lebih cepat dan efisien',
        'tip_category' => 'Pilih kategori yang tepat untuk memudahkan pengelompokan kontak',
        'tip_schedule' => 'Jadwalkan scraping pada waktu yang sesuai untuk performa optimal',
        'tip_auto' => 'Kontak yang berhasil di-scrape akan otomatis tersimpan dalam database',
        
        // Buttons
        'btn_create' => 'Mulai Scraping',
        'btn_update' => 'Simpan Perubahan',
        
        // Status
        'status_pending' => 'Menunggu',
        'status_processing' => 'Sedang Proses',
        'status_completed' => 'Selesai',
        'status_failed' => 'Gagal',
        
        // Messages
        'success_create' => 'Scraping kontak berhasil dijadwalkan',
        'success_update' => 'Scraping kontak berhasil diperbarui',
        'success_delete' => 'Scraping kontak berhasil dihapus',
        'error_create' => 'Gagal membuat scraping kontak',
        'error_update' => 'Gagal memperbarui scraping kontak',
        'error_delete' => 'Gagal menghapus scraping kontak',
        
        // Meta
        'title' => 'Scraping Kontak',
        'description' => 'Ekstrak kontak dari WhatsApp berdasarkan device dan kategori',
    ],
    
    'group' => [
        // Page Titles & Navigation
        'page_title' => 'Scraping Grup',
        'create_title' => 'Tambah Scraping Grup',
        'update_title' => 'Edit Scraping Grup',
        'back_to' => 'Kembali ke Scraping',
        'list_title' => 'Daftar Data Scrap Group Whatsapp',
        'whatsapp_group' => 'Grup WhatsApp',
        'group_list' => 'Daftar Grup WhatsApp',

        // table columns
        'device' => 'Perangkat Device',
        'group_id' => 'ID Grup',
        'contacts' => 'Kontak',
        'syncron' => 'Sinkronisasi',
        
        // Card Headers
        'scraping_info' => 'Konfigurasi Scraping',
        
        // Form Labels
        'devices' => 'Perangkat Device',
        'name' => 'Nama Scraping',
        'schedule' => 'Jadwal Scraping',
        
        // Placeholders
        'name_placeholder' => 'Contoh: Scraping Grup Sales Team',
        'choose_devices' => 'Pilih perangkat...',
        
        // Helper Texts
        'devices_help' => 'Pilih device WhatsApp yang akan di-scrape grup-nya',
        'name_help' => 'Nama untuk identifikasi proses scraping grup ini',
        'schedule_help' => 'Jadwal kapan proses scraping grup akan dijalankan',
        
        // Labels
        'required' => 'Wajib diisi',
        'required_field' => 'Wajib diisi',
        
        // Tips & Alerts
        'tips_title' => 'Tips Scraping Grup WhatsApp:',
        'tip_device' => 'Proses akan mengambil daftar semua grup dari device yang dipilih',
        'tip_category' => 'Gunakan multiple device untuk mendapatkan lebih banyak grup',
        'tip_schedule' => 'Data grup akan disimpan untuk digunakan dalam kampanye broadcast',
        'tip_auto' => 'Pastikan device dalam kondisi online saat proses scraping',
        
        // Buttons
        'btn_create' => 'Mulai Scraping',
        'btn_update' => 'Simpan Perubahan',
        
        // Status
        'status_pending' => 'Menunggu',
        'status_processing' => 'Sedang Proses',
        'status_completed' => 'Selesai',
        'status_failed' => 'Gagal',
        
        // Messages
        'success_create' => 'Scraping grup berhasil dijadwalkan',
        'success_update' => 'Scraping grup berhasil diperbarui',
        'success_delete' => 'Scraping grup berhasil dihapus',
        'error_create' => 'Gagal membuat scraping grup',
        'error_update' => 'Gagal memperbarui scraping grup',
        'error_delete' => 'Gagal menghapus scraping grup',
        
        // Meta
        'title' => 'Scraping Grup',
        'description' => 'Ekstrak data dari grup dan komunitas WhatsApp',
    ],
];