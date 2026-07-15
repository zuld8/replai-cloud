<?php

namespace App\Support;

/**
 * ButtonFormatter — Normalizer tombol/list ke format standar CRM.
 *
 * Format output: [{"text":"...", "type":"reply|url", "url":"..."}]
 * chat.vue: b.type === 'url' → link, selain itu → chip reply.
 *
 * Menangani semua sumber input:
 *   - Template Meta (QUICK_REPLY / URL)
 *   - Broadcast (quick_reply / url)
 *   - Flow options (label-based)
 *   - Interactive payload WA
 */
class ButtonFormatter
{
    /**
     * Normalisasi raw button data ke JSON string siap simpan di DB.
     *
     * @param  mixed  $raw  Array, JSON string, atau null
     * @return string|null  JSON string atau null jika kosong
     */
    public static function format($raw): ?string
    {
        if (empty($raw)) return null;

        // Decode jika masih string JSON
        $items = is_string($raw) ? (json_decode($raw, true) ?: []) : $raw;
        if (!is_array($items) || empty($items)) return null;

        $normalized = [];
        foreach ($items as $b) {
            $text = $b['text'] ?? $b['title'] ?? $b['label'] ?? '';
            if (empty(trim($text))) continue;

            // Normalisasi type → selalu huruf kecil 'url' atau 'reply'
            $type = strtolower($b['type'] ?? $b['sub_type'] ?? '');
            $isUrl = in_array($type, ['url', 'link', 'url_button']);

            $btn = [
                'text' => trim($text),
                'type' => $isUrl ? 'url' : 'reply',
            ];

            // URL hanya disertakan kalau tipe url
            if ($isUrl && !empty($b['url'])) {
                $btn['url'] = $b['url'];
            }

            $normalized[] = $btn;
        }

        return empty($normalized) ? null : json_encode($normalized);
    }

    /**
     * Normalisasi dari collection ChatFlowOption (flow engine).
     * Setiap option jadi chip reply (flow tidak pakai URL).
     */
    public static function fromFlowOptions($options): ?string
    {
        if (empty($options)) return null;

        $normalized = [];
        foreach ($options as $o) {
            $text = trim($o->label ?? $o['label'] ?? '');
            if (empty($text)) continue;
            $normalized[] = ['text' => $text, 'type' => 'reply'];
        }

        return empty($normalized) ? null : json_encode($normalized);
    }
}
