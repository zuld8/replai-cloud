<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChatFlow\ChatFlow;
use App\Models\ChatFlow\ChatFlowNode;
use App\Models\ChatFlow\ChatFlowOption;
use Ramsey\Uuid\Uuid;

/**
 * ChatFlowDemoSeeder — Buat 1 flow contoh untuk testing P1.
 *
 * Jalankan: php artisan db:seed --class=ChatFlowDemoSeeder
 *
 * Wajib set BUSINESS_ID di .env atau ubah langsung di variabel $businessId di bawah.
 * Gunakan business_id yang sama dengan WABA device yang akan diuji.
 */
class ChatFlowDemoSeeder extends Seeder
{
    public function run(): void
    {
        // ── Konfigurasi ──────────────────────────────────────────
        // Ambil business_id dari tabel, ATAU set manual di sini
        $businessId = \DB::table('history_chats')
            ->whereNotNull('business_id')
            ->value('business_id');

        if (!$businessId) {
            $this->command->error('Tidak ada business_id di history_chats. Set manual di seeder.');
            return;
        }

        $this->command->info("Menggunakan business_id: {$businessId}");

        // ── Hapus flow demo lama jika ada ───────────────────────
        $old = \DB::table('chat_flows')->where('name', 'Demo — Menu ONPOINT')->where('business_id', $businessId)->first();
        if ($old) {
            $nodes = \DB::table('chat_flow_nodes')->where('flow_id', $old->id)->pluck('id');
            \DB::table('chat_flow_options')->whereIn('node_id', $nodes)->delete();
            \DB::table('chat_flow_nodes')->where('flow_id', $old->id)->delete();
            \DB::table('chat_flow_sessions')->where('flow_id', $old->id)->delete();
            \DB::table('chat_flows')->where('id', $old->id)->delete();
            $this->command->info('Flow demo lama dihapus.');
        }

        // ── Buat Flow ────────────────────────────────────────────
        $flowId = Uuid::uuid4()->toString();

        // ── Node 1: Pesan pembuka (buttons) ─────────────────────
        $n1 = $this->node($flowId, 'buttons', "Halo! 👋 Selamat datang.\n\nSilakan pilih menu:", 0);

        // ── Node 2: FAQ list ─────────────────────────────────────
        $n2 = $this->node($flowId, 'list', "Pilih pertanyaan yang ingin kamu tanyakan:", 1, null, null, 'Pilih');

        // ── Node 3a: Jawaban cara daftar ─────────────────────────
        $n3a = $this->node($flowId, 'buttons', "📝 *Cara Daftar*\n\nKamu bisa daftar langsung di website kami atau hubungi CS kami.", 2);

        // ── Node 3b: Jawaban harga ───────────────────────────────
        $n3b = $this->node($flowId, 'buttons', "💰 *Info Harga*\n\nHarga mulai dari Rp 99.000/bulan. Cek paket lengkap di website kami.", 3);

        // ── Node 3c: Jam operasional ─────────────────────────────
        $n3c = $this->node($flowId, 'buttons', "🕐 *Jam Operasional*\n\nSenin–Jumat: 08.00–17.00 WIB\nSabtu: 09.00–13.00 WIB", 4);

        // ── Node 4: Handoff CS ───────────────────────────────────
        $n4 = $this->node($flowId, 'handoff', "⏳ Menghubungkan ke CS kami...\n\nTunggu sebentar ya, CS kami akan segera membantu kamu!", 5);

        // ── Buat Flow (start_node = n1) ──────────────────────────
        \DB::table('chat_flows')->insert([
            'id'                 => $flowId,
            'business_id'        => $businessId,
            'name'               => 'Demo — Menu ONPOINT',
            'trigger_type'       => 'keyword',
            'trigger_keywords'   => json_encode(['halo', 'menu', 'mulai', 'hi', 'hello']),
            'channels'           => json_encode([]),  // kosong = semua channel
            'start_node_id'      => $n1,
            'fallback_action'    => 'repeat_menu',
            'session_timeout_min'=> 30,
            'status'             => 'active',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        $this->command->info("Flow '{$flowId}' dibuat.");

        // ── Options Node 1 (buttons) ─────────────────────────────
        $this->option($n1, 'button', 'FAQ',     null, null, 0, 'goto_node', $n2,  'opt_'.$n1.'_faq');
        $this->option($n1, 'button', 'Chat CS', null, null, 1, 'handoff',   $n4,  'opt_'.$n1.'_cs');

        // ── Options Node 2 (list) ────────────────────────────────
        $this->option($n2, 'list_row', 'Cara Daftar',        'Langkah-langkah pendaftaran',  null, 0, 'goto_node', $n3a, 'opt_'.$n2.'_daftar');
        $this->option($n2, 'list_row', 'Info Harga',         'Paket & harga layanan',        null, 1, 'goto_node', $n3b, 'opt_'.$n2.'_harga');
        $this->option($n2, 'list_row', 'Jam Operasional',    'Waktu layanan CS',             null, 2, 'goto_node', $n3c, 'opt_'.$n2.'_jam');

        // ── Options Node 3a/b/c (kembali atau ke CS) ─────────────
        foreach ([$n3a, $n3b, $n3c] as $idx => $nId) {
            $this->option($nId, 'button', 'Menu Utama', null, null, 0, 'back_to_start', null, 'opt_'.$nId.'_back');
            $this->option($nId, 'button', 'Chat CS',    null, null, 1, 'handoff',       $n4,  'opt_'.$nId.'_cs');
        }

        $this->command->info("✅ Seeder selesai. Kirim 'halo' ke WABA untuk tes.");
        $this->command->line("   Flow ID : {$flowId}");
        $this->command->line("   Start   : {$n1}");
    }

    private function node(string $flowId, string $type, string $body, int $pos, ?string $header = null, ?string $footer = null, ?string $listLabel = null): string
    {
        $id = Uuid::uuid4()->toString();
        \DB::table('chat_flow_nodes')->insert([
            'id'               => $id,
            'flow_id'          => $flowId,
            'type'             => $type,
            'body_text'        => $body,
            'header'           => $header,
            'footer'           => $footer,
            'list_button_label'=> $listLabel,
            'position'         => $pos,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
        return $id;
    }

    private function option(
        string $nodeId, string $kind, string $label,
        ?string $desc, ?string $section, int $order,
        string $action, ?string $targetNodeId, string $replyId
    ): void {
        \DB::table('chat_flow_options')->insert([
            'id'             => Uuid::uuid4()->toString(),
            'node_id'        => $nodeId,
            'kind'           => $kind,
            'label'          => mb_substr($label, 0, $kind === 'button' ? 20 : 24),
            'description'    => $desc ? mb_substr($desc, 0, 72) : null,
            'section'        => $section,
            'order'          => $order,
            'target_action'  => $action,
            'target_node_id' => $targetNodeId,
            'reply_id'       => $replyId,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }
}
