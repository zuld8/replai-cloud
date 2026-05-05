<?php

namespace Database\Seeders;

use App\Models\ModulFiture;
use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModulFiturePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            [
                'name' => 'Live Chat Widget',
                'slug' => 'live-chat-widget',
                'description' => 'Widget live chat untuk website',
                'icon' => 'message-circle',
                'order' => 1,
                'permissions' => [
                    ['action' => 'lihat', 'description' => 'Melihat daftar live chat widget'],
                    ['action' => 'tambah', 'description' => 'Menambah live chat widget baru'],
                    ['action' => 'edit', 'description' => 'Mengubah live chat widget'],
                    ['action' => 'hapus', 'description' => 'Menghapus live chat widget'],
                ]
            ],
            [
                'name' => 'Human Agents',
                'slug' => 'human-agents',
                'description' => 'Manajemen agen customer service',
                'icon' => 'users',
                'order' => 2,
                'permissions' => [
                    ['action' => 'lihat', 'description' => 'Melihat daftar agen'],
                    ['action' => 'tambah', 'description' => 'Menambah agen baru'],
                    ['action' => 'edit', 'description' => 'Mengubah data agen'],
                    ['action' => 'hapus', 'description' => 'Menghapus agen'],
                ]
            ],
            [
                'name' => 'WhatsApp Broadcast',
                'slug' => 'whatsapp-broadcast',
                'description' => 'Broadcast pesan WhatsApp massal',
                'icon' => 'send',
                'order' => 3,
                'permissions' => [
                    ['action' => 'lihat', 'description' => 'Melihat daftar broadcast'],
                    ['action' => 'tambah', 'description' => 'Membuat broadcast baru'],
                    ['action' => 'edit', 'description' => 'Mengubah broadcast'],
                    ['action' => 'hapus', 'description' => 'Menghapus broadcast'],
                ]
            ],
            [
                'name' => 'Up Selling',
                'slug' => 'up-selling',
                'description' => 'Fitur up selling produk',
                'icon' => 'trending-up',
                'order' => 4,
                'permissions' => [
                    ['action' => 'lihat', 'description' => 'Melihat kampanye up selling'],
                    ['action' => 'tambah', 'description' => 'Membuat kampanye up selling'],
                    ['action' => 'edit', 'description' => 'Mengubah kampanye up selling'],
                    ['action' => 'hapus', 'description' => 'Menghapus kampanye up selling'],
                ]
            ],
            [
                'name' => 'Scraping Google Maps',
                'slug' => 'scraping-google-maps',
                'description' => 'Scraping data dari Google Maps',
                'icon' => 'map',
                'order' => 5,
                'permissions' => [
                    ['action' => 'lihat', 'description' => 'Melihat hasil scraping Google Maps'],
                    ['action' => 'tambah', 'description' => 'Membuat tugas scraping baru'],
                    ['action' => 'edit', 'description' => 'Mengubah tugas scraping'],
                    ['action' => 'hapus', 'description' => 'Menghapus tugas scraping'],
                ]
            ],
            [
                'name' => 'Scraping Nomor HP',
                'slug' => 'scraping-nomor-hp',
                'description' => 'Scraping nomor HP dari berbagai sumber',
                'icon' => 'phone',
                'order' => 6,
                'permissions' => [
                    ['action' => 'lihat', 'description' => 'Melihat hasil scraping nomor HP'],
                    ['action' => 'tambah', 'description' => 'Membuat tugas scraping nomor HP'],
                    ['action' => 'edit', 'description' => 'Mengubah tugas scraping'],
                    ['action' => 'hapus', 'description' => 'Menghapus tugas scraping'],
                ]
            ],
            [
                'name' => 'Scraping Group WhatsApp',
                'slug' => 'scraping-group-whatsapp',
                'description' => 'Scraping data dari grup WhatsApp',
                'icon' => 'users',
                'order' => 7,
                'permissions' => [
                    ['action' => 'lihat', 'description' => 'Melihat hasil scraping grup WhatsApp'],
                    ['action' => 'tambah', 'description' => 'Membuat tugas scraping grup'],
                    ['action' => 'edit', 'description' => 'Mengubah tugas scraping'],
                    ['action' => 'hapus', 'description' => 'Menghapus tugas scraping'],
                ]
            ],
            [
                'name' => 'Chatbot',
                'slug' => 'chatbot',
                'description' => 'Chatbot otomatis',
                'icon' => 'bot',
                'order' => 8,
                'permissions' => [
                    ['action' => 'lihat', 'description' => 'Melihat daftar chatbot'],
                    ['action' => 'tambah', 'description' => 'Membuat chatbot baru'],
                    ['action' => 'edit', 'description' => 'Mengubah konfigurasi chatbot'],
                    ['action' => 'hapus', 'description' => 'Menghapus chatbot'],
                ]
            ],
            [
                'name' => 'AI Agent',
                'slug' => 'ai-agent',
                'description' => 'AI Agent untuk customer service',
                'icon' => 'sparkles',
                'order' => 9,
                'permissions' => [
                    ['action' => 'lihat', 'description' => 'Melihat daftar AI agent'],
                    ['action' => 'tambah', 'description' => 'Membuat AI agent baru'],
                    ['action' => 'edit', 'description' => 'Mengubah konfigurasi AI agent'],
                    ['action' => 'hapus', 'description' => 'Menghapus AI agent'],
                ]
            ],
            [
                'name' => 'WhatsApp Un Official',
                'slug' => 'whatsapp-unofficial',
                'description' => 'Koneksi WhatsApp via WA Web',
                'icon' => 'message-square',
                'order' => 10,
                'permissions' => [
                    ['action' => 'lihat', 'description' => 'Melihat daftar koneksi WhatsApp'],
                    ['action' => 'tambah', 'description' => 'Menambah koneksi WhatsApp baru'],
                    ['action' => 'edit', 'description' => 'Mengubah koneksi WhatsApp'],
                    ['action' => 'hapus', 'description' => 'Menghapus koneksi WhatsApp'],
                ]
            ],
            [
                'name' => 'WhatsApp Official',
                'slug' => 'whatsapp-official',
                'description' => 'WhatsApp Business API Official',
                'icon' => 'check-circle',
                'order' => 11,
                'permissions' => [
                    ['action' => 'lihat', 'description' => 'Melihat daftar akun WABA'],
                    ['action' => 'tambah', 'description' => 'Menambah akun WABA baru'],
                    ['action' => 'edit', 'description' => 'Mengubah konfigurasi WABA'],
                    ['action' => 'hapus', 'description' => 'Menghapus akun WABA'],
                ]
            ],
            [
                'name' => 'Telegram',
                'slug' => 'telegram',
                'description' => 'Integrasi dengan Telegram',
                'icon' => 'send',
                'order' => 12,
                'permissions' => [
                    ['action' => 'lihat', 'description' => 'Melihat daftar bot Telegram'],
                    ['action' => 'tambah', 'description' => 'Menambah bot Telegram baru'],
                    ['action' => 'edit', 'description' => 'Mengubah konfigurasi bot'],
                    ['action' => 'hapus', 'description' => 'Menghapus bot Telegram'],
                ]
            ],
            [
                'name' => 'Instagram',
                'slug' => 'instagram',
                'description' => 'Integrasi dengan Instagram',
                'icon' => 'instagram',
                'order' => 13,
                'permissions' => [
                    ['action' => 'lihat', 'description' => 'Melihat daftar akun Instagram'],
                    ['action' => 'tambah', 'description' => 'Menambah akun Instagram baru'],
                    ['action' => 'edit', 'description' => 'Mengubah konfigurasi Instagram'],
                    ['action' => 'hapus', 'description' => 'Menghapus akun Instagram'],
                ]
            ],
            [
                'name' => 'Messenger',
                'slug' => 'messenger',
                'description' => 'Integrasi dengan Facebook Messenger',
                'icon' => 'facebook',
                'order' => 14,
                'permissions' => [
                    ['action' => 'lihat', 'description' => 'Melihat daftar akun Messenger'],
                    ['action' => 'tambah', 'description' => 'Menambah akun Messenger baru'],
                    ['action' => 'edit', 'description' => 'Mengubah konfigurasi Messenger'],
                    ['action' => 'hapus', 'description' => 'Menghapus akun Messenger'],
                ]
            ],
            [
                'name' => 'WhatsApp Template',
                'slug' => 'whatsapp-template',
                'description' => 'Template pesan WhatsApp',
                'icon' => 'file-text',
                'order' => 15,
                'permissions' => [
                    ['action' => 'lihat', 'description' => 'Melihat daftar template pesan'],
                    ['action' => 'tambah', 'description' => 'Membuat template pesan baru'],
                    ['action' => 'edit', 'description' => 'Mengubah template pesan'],
                    ['action' => 'hapus', 'description' => 'Menghapus template pesan'],
                ]
            ],
            [
                'name' => 'Contact Data',
                'slug' => 'contact-data',
                'description' => 'Database kontak pelanggan',
                'icon' => 'user',
                'order' => 16,
                'permissions' => [
                    ['action' => 'lihat', 'description' => 'Melihat daftar kontak'],
                    ['action' => 'tambah', 'description' => 'Menambah kontak baru'],
                    ['action' => 'edit', 'description' => 'Mengubah data kontak'],
                    ['action' => 'hapus', 'description' => 'Menghapus kontak'],
                ]
            ],
            [
                'name' => 'Kategori Kontak',
                'slug' => 'kategori-kontak',
                'description' => 'Kategori untuk kontak',
                'icon' => 'folder',
                'order' => 17,
                'permissions' => [
                    ['action' => 'lihat', 'description' => 'Melihat daftar kategori'],
                    ['action' => 'tambah', 'description' => 'Membuat kategori baru'],
                    ['action' => 'edit', 'description' => 'Mengubah kategori'],
                    ['action' => 'hapus', 'description' => 'Menghapus kategori'],
                ]
            ],
            [
                'name' => 'Tag Manager',
                'slug' => 'tag-manager',
                'description' => 'Manajemen tag untuk kontak',
                'icon' => 'tag',
                'order' => 18,
                'permissions' => [
                    ['action' => 'lihat', 'description' => 'Melihat daftar tag'],
                    ['action' => 'tambah', 'description' => 'Membuat tag baru'],
                    ['action' => 'edit', 'description' => 'Mengubah tag'],
                    ['action' => 'hapus', 'description' => 'Menghapus tag'],
                ]
            ],
            [
                'name' => 'Media Manager',
                'slug' => 'media-manager',
                'description' => 'Manajemen file dan media',
                'icon' => 'image',
                'order' => 19,
                'permissions' => [
                    ['action' => 'tambah-folder', 'description' => 'Membuat folder baru'],
                    ['action' => 'hapus-folder', 'description' => 'Menghapus folder'],
                    ['action' => 'upload-media', 'description' => 'Mengupload file media'],
                    ['action' => 'hapus-media', 'description' => 'Menghapus file media'],
                ]
            ],
            [
                'name' => 'Laporan',
                'slug' => 'laporan',
                'description' => 'Laporan dan analytics',
                'icon' => 'bar-chart',
                'order' => 20,
                'permissions' => [
                    ['action' => 'lihat', 'description' => 'Melihat laporan dan statistik'],
                    ['action' => 'tambah', 'description' => 'Membuat laporan custom'],
                    ['action' => 'edit', 'description' => 'Mengubah laporan'],
                    ['action' => 'hapus', 'description' => 'Menghapus laporan'],
                ]
            ],
            [
                'name' => 'CRM Chats',
                'slug' => 'crm-chats',
                'description' => 'Chat CRM customer',
                'icon' => 'message-circle',
                'order' => 21,
                'permissions' => [
                    ['action' => 'lihat-crm', 'description' => 'Melihat percakapan CRM'],
                    ['action' => 'balas-crm', 'description' => 'Membalas pesan customer'],
                    ['action' => 'edit-informasi', 'description' => 'Mengubah informasi customer'],
                    ['action' => 'assign-agent', 'description' => 'Assign agen ke percakapan'],
                ]
            ],
            [
                'name' => 'Kanban',
                'slug' => 'kanban',
                'description' => 'Kanban board untuk pipeline',
                'icon' => 'trello',
                'order' => 22,
                'permissions' => [
                    ['action' => 'drag-drop-kanban', 'description' => 'Memindahkan kartu di kanban'],
                    ['action' => 'kelola-pipeline', 'description' => 'Mengelola pipeline kanban'],
                    ['action' => 'kelola-label', 'description' => 'Mengelola label kanban'],
                ]
            ],
            [
                'name' => 'Ticket',
                'slug' => 'ticket',
                'description' => 'Sistem ticketing',
                'icon' => 'ticket',
                'order' => 23,
                'permissions' => [
                    ['action' => 'drag-drop', 'description' => 'Memindahkan status ticket'],
                    ['action' => 'tambah-ticket', 'description' => 'Membuat ticket baru'],
                    ['action' => 'edit', 'description' => 'Mengubah ticket'],
                    ['action' => 'hapus', 'description' => 'Menghapus ticket'],
                    ['action' => 'kelola-label', 'description' => 'Mengelola label ticket'],
                    ['action' => 'kelola-kategori', 'description' => 'Mengelola kategori ticket'],
                ]
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Kelola Bisnis',
                'icon' => 'business',
                'order' => 24,
                'permissions' => [
                    ['action' => 'lihat', 'description' => 'Melihat daftar bisnis'],
                    ['action' => 'tambah', 'description' => 'Menambah bisnis baru'],
                    ['action' => 'edit', 'description' => 'Mengubah data bisnis'],
                    ['action' => 'hapus', 'description' => 'Menghapus bisnis'],
                ]
            ],
            [
                'name' => 'Transaksi',
                'slug' => 'transaction',
                'description' => 'Kelola Transaksi',
                'icon' => 'transaction',
                'order' => 25,
                'permissions' => [
                    ['action' => 'lihat', 'description' => 'Melihat riwayat transaksi'],
                    ['action' => 'subs', 'description' => 'Kelola transaksi subscription'],
                    ['action' => 'credit-ai', 'description' => 'Kelola pembelian kredit AI'],
                    ['action' => 'credit-storage', 'description' => 'Kelola pembelian kredit storage'],
                    ['action' => 'credit-mua', 'description' => 'Kelola pembelian kredit MUA (Multi User Access)'],
                ]
            ],
            [
                'name' => 'Role Permission',
                'slug' => 'role',
                'description' => 'Kelola Akses Pengguna',
                'icon' => 'shield',
                'order' => 26,
                'permissions' => [
                    ['action' => 'lihat', 'description' => 'Melihat daftar role & permission'],
                    ['action' => 'tambah', 'description' => 'Membuat role baru'],
                    ['action' => 'edit', 'description' => 'Mengubah role & assign permission'],
                    ['action' => 'hapus', 'description' => 'Menghapus role'],
                ]
            ],
        ];

        foreach ($modules as $moduleData) {
            // Create module
            $module = ModulFiture::create([
                'name' => $moduleData['name'],
                'slug' => $moduleData['slug'],
                'description' => $moduleData['description'],
                'icon' => $moduleData['icon'],
                'order' => $moduleData['order'],
                'is_active' => true,
            ]);

            // Create permissions for this module
            $permissionIds = [];
            foreach ($moduleData['permissions'] as $permissionData) {
                $permissionName = $moduleData['slug'] . '.' . $permissionData['action'];

                $permission = Permission::firstOrCreate(
                    ['name' => $permissionName],
                    [
                        'guard_name' => 'web',
                        'name' => $permissionName,
                        'description' => $permissionData['description'],
                    ]
                );

                $permissionIds[] = $permission->id;
            }

            // Attach permissions to module
            $module->permissions()->attach($permissionIds);

            $this->command->info("✓ Created module: {$module->name} with " . count($permissionIds) . " permissions");
        }

        $this->command->info("\n✓ All modules and permissions created successfully!");
        $this->command->info("Total Modules: " . ModulFiture::count());
        $this->command->info("Total Permissions: " . Permission::count());
    }
}
