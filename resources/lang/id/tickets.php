<?php

return [
    // Ticket Management
    'ticket_management' => 'Manajemen Tiket',
    'contact_list' => 'Daftar Tiket',
    'total' => 'Total',
    'contact' => 'Kontak',
    'search_contact' => 'Cari kontak berdasarkan nama, telepon, atau email...',
    'contacts_found' => 'kontak ditemukan',
    'select_contact' => 'Pilih Kontak',
    
    // Filters
    'start_date' => 'Tanggal Mulai',
    'end_date' => 'Tanggal Selesai',
    'level' => 'Level',
    'agent' => 'Agen',
    'tickets' => 'tiket',
    'all_categories' => 'Semua Kategori',
    'all_levels' => 'Semua Level',
    'all_agents' => 'Semua Agen',
    'all_status' => 'Semua Status',
    'no_ticket_in_column' => 'Tidak ada tiket di kolom ini',
    
    // Ticket Levels
    'level_low' => 'Rendah',
    'level_medium' => 'Sedang',
    'level_high' => 'Tinggi',
    'level_urgent' => 'Mendesak',
    
    // Ticket Information
    'ticket_id' => 'ID Tiket',
    'ticket_level' => 'Level Tiket',
    'ticket_name' => 'Nama Tiket',
    'ticket_name_placeholder' => 'Masukkan nama tiket',
    'ticket_detail' => 'Detail Tiket',
    
    // Status
    'status' => 'Status',
    'status_open' => 'Terbuka',
    'status_resolved' => 'Terselesaikan',
    'status_pending' => 'Tertunda',
    'status_block' => 'Diblokir',
    'status_in_progress' => 'Sedang Dikerjakan',
    'status_closed' => 'Ditutup',
    
    // Basic Info
    'basic_info' => 'Informasi Dasar',
    'name' => 'Nama',
    'email' => 'Email',
    'phone' => 'Telepon',
    'title' => 'Judul',
    'title_placeholder' => 'Masukkan judul tiket',
    'priority' => 'Prioritas',
    
    // Category
    'category' => 'Kategori',
    'select_category' => 'Pilih Kategori',
    'category_name' => 'Nama Kategori',
    'category_name_placeholder' => 'Masukkan nama kategori',
    'category_slug' => 'Slug Kategori',
    'category_slug_placeholder' => 'slug-otomatis',
    'category_slug_hint' => 'Kosongkan untuk membuat otomatis dari nama',
    'category_description' => 'Deskripsi',
    'category_description_placeholder' => 'Masukkan deskripsi kategori',
    'category_active' => 'Aktif',
    'category_management' => 'Manajemen Kategori',
    'manage_categories' => 'Kelola Kategori',
    'add_category' => 'Tambah Kategori',
    'edit_category' => 'Edit Kategori',
    'create_category' => 'Buat Kategori',
    'update_category' => 'Update Kategori',
    'search_category' => 'Cari kategori...',
    'no_categories' => 'Tidak ada kategori ditemukan',
    'category_deleted' => 'Kategori berhasil dihapus',
    'categories_deleted' => 'Kategori berhasil dihapus',
    'category_updated' => 'Kategori berhasil diupdate',
    'failed_delete_category' => 'Gagal menghapus kategori',
    'failed_update_category' => 'Gagal mengupdate kategori',
    'confirm_delete_category_title' => 'Hapus Kategori?',
    'confirm_delete_category_text' => 'Apakah Anda yakin ingin menghapus kategori ini?',
    'confirm_bulk_delete_categories_title' => 'Hapus Beberapa Kategori?',
    'confirm_bulk_delete_categories_text' => 'Apakah Anda yakin ingin menghapus {count} kategori?',
    
    // Channel
    'channel_info' => 'Informasi Channel',
    'source_channel' => 'Channel Sumber',
    
    // Assignment
    'status_assignment' => 'Status & Penugasan',
    'assign_agent' => 'Tugaskan Agen',
    'handled_by' => 'Ditangani Oleh',
    'resolved_by' => 'Diselesaikan Oleh',
    'assigned_to' => 'Ditugaskan ke',
    'no_agent' => 'Tanpa Agen',
    'no_agent_assigned' => 'Belum ada agen yang ditugaskan',
    'not_handled' => 'Belum Ditangani',
    'hold_ctrl_multiple' => 'Tahan Ctrl/Cmd untuk memilih beberapa agen',
    
    // Label
    'label' => 'Label',
    'select_label' => 'Pilih Label',
    'label_name' => 'Nama Label',
    'label_name_placeholder' => 'Masukkan nama label',
    'label_tag' => 'Tag/Kata Kunci Label',
    'label_tag_desc' => 'Kata kunci yang akan memicu penugasan label ini',
    'label_tag_placeholder' => 'contoh: mendesak, keluhan, tagihan',
    'label_position' => 'Indeks Posisi',
    'label_color' => 'Warna Label',
    'label_selection_hint' => 'Label akan ditentukan otomatis berdasarkan kata kunci jika tidak dipilih',
    'add_label' => 'Tambah Label',
    'edit_label' => 'Edit Label',
    'delete_label' => 'Hapus Label',
    'update_label' => 'Update Label',
    'save_label' => 'Simpan Label',
    'label_created' => 'Label berhasil dibuat',
    'label_updated' => 'Label berhasil diupdate',
    'label_deleted' => 'Label berhasil dihapus',
    'failed_create_label' => 'Gagal membuat label',
    'failed_update_label' => 'Gagal mengupdate label',
    'failed_delete_label' => 'Gagal menghapus label',
    'confirm_delete_label_title' => 'Hapus Label?',
    'confirm_delete_label_text' => 'Apakah Anda yakin ingin menghapus label ini? Semua tiket di label ini akan dipindahkan ke "Tiket Baru".',
    'yes_delete_label' => 'Ya, Hapus Label!',
    'label_changed' => 'Label Diubah',
    'move_to_label' => 'Pindah ke Label',
    'ticket_moved' => 'Tiket berhasil dipindahkan',
    'failed_move_ticket' => 'Gagal memindahkan tiket',
    
    // Ticket Actions
    'add_ticket' => 'Buat Tiket',
    'edit_ticket' => 'Edit Tiket',
    'create_ticket' => 'Buat Tiket',
    'update_ticket' => 'Update Tiket',
    'select_contact' => 'Pilih Kontak',
    
    // Notes
    'notes' => 'Catatan',
    'notes_placeholder' => 'Masukkan catatan atau deskripsi',
    'add' => 'Tambah',
    'add_note_placeholder' => 'Tambahkan catatan...',
    'no_notes_yet' => 'Belum ada catatan. Jadilah yang pertama menambahkan!',
    'ctrl_enter_to_send' => 'Tekan Ctrl+Enter untuk mengirim',
    
    // Activity
    'activity_history' => 'Riwayat Aktivitas',
    'no_activity_logs' => 'Tidak ada log aktivitas tersedia',
    
    // Attachment
    'attachment' => 'Lampiran',
    'file_upload_hint' => 'Format yang didukung: JPG, PNG, PDF, DOC, DOCX, TXT',
    
    // Timestamps
    'timestamps' => 'Waktu',
    'created_at' => 'Dibuat Pada',
    'updated_at' => 'Diupdate Pada',
    
    // Actions
    'actions' => 'Aksi',
    'view_detail' => 'Lihat Detail',
    'edit' => 'Edit',
    'delete' => 'Hapus',
    'close' => 'Tutup',
    'cancel' => 'Batal',
    'save' => 'Simpan',
    'manage' => 'Kelola',
    'quick_actions' => 'Aksi Cepat',
    
    // Pagination & Loading
    'loading_data' => 'Memuat Data...',
    'loading_more' => 'Memuat Lebih Banyak...',
    'load_more' => 'Muat Lebih Banyak',
    'remaining' => 'Tersisa',
    'contact_per_column' => 'Kontak per Kolom',
    'contact_loaded' => 'kontak dimuat',
    
    // Status Messages
    'active' => 'Aktif',
    'inactive' => 'Tidak Aktif',
    'selected' => 'terpilih',
    'delete_selected' => 'Hapus Terpilih',
    
    // Confirmations
    'confirm_delete_title' => 'Apakah Anda yakin?',
    'confirm_delete_text' => 'Anda tidak akan dapat mengembalikan ini!',
    'yes_delete' => 'Ya, hapus!',
    'contact_deleted' => 'Kontak berhasil dihapus.',
    
    // Error Messages
    'failed_delete_contact' => 'Gagal menghapus kontak',
    'failed_load_data' => 'Gagal memuat data',
    'error_load_data' => 'Error memuat data',
];
