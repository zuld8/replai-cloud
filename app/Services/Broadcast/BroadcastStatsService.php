<?php

namespace App\Services\Broadcast;

/**
 * BroadcastStatsService — SATU sumber kebenaran kalkulasi & render status broadcast WABA.
 *
 * Prinsip funnel mutually-exclusive:
 *   total = pending + delivered + read + failed  (tiap pesan = 1 bucket)
 *   reached = delivered + read  (sampai HP)
 *   deliveryRate = reached / total * 100
 *
 * STATUS (penyelesaian proses) dipisah dari METRIK (deliveryRate):
 *   pending > 0 → "Berjalan"  |  failed > reached → "Banyak Gagal"  |  else → "Selesai"
 */
class BroadcastStatsService
{
    /**
     * Normalize stat columns into mutually-exclusive funnel.
     *
     * @param  int  $total     stat_total
     * @param  int  $delivered stat_delivered
     * @param  int  $read      stat_read (DIBACA — bukan antrian!)
     * @param  int  $failed    stat_failed + stat_delivery_failed
     * @return array{total,delivered,read,failed,pending,reached,deliveryRate,status,label,color}
     */
    public static function normalize(int $total, int $delivered, int $read, int $failed): array
    {
        // Clamp negatives
        $delivered = max(0, $delivered);
        $read      = max(0, $read);
        $failed    = max(0, $failed);

        // Cap so sum never exceeds total (defensive against webhook double-counting)
        $accounted = min($total, $delivered + $read + $failed);
        $pending   = max(0, $total - $accounted);

        $reached      = $delivered + $read;
        $deliveryRate = $total > 0 ? round($reached / $total * 100) : 0;

        // STATUS = completion state (bukan kualitas)
        if ($total === 0) {
            $status = 'menunggu'; $label = 'Menunggu'; $color = '#64748B';
        } elseif ($pending > 0) {
            $status = 'berjalan'; $label = 'Berjalan'; $color = '#2E8DE1';
        } elseif ($failed > $reached) {
            $status = 'gagal';    $label = 'Banyak Gagal'; $color = '#DC2626';
        } else {
            $status = 'selesai';  $label = 'Selesai';  $color = '#16A34A';
        }

        return compact('total', 'delivered', 'read', 'failed', 'pending', 'reached', 'deliveryRate', 'status', 'label', 'color');
    }

    /**
     * Render HTML kolom Status Pengiriman — segmented bar + badge + angka.
     * Dipakai di KEDUA DataTable controller agar tampilan identik.
     *
     * @param  int    $total
     * @param  int    $delivered    stat_delivered
     * @param  int    $read         stat_read (DIBACA)
     * @param  int    $failed       stat_failed + stat_delivery_failed
     * @param  string $dbStatus     bw.status (untuk detect processing)
     * @return string HTML
     */
    public static function renderStatsCol(int $total, int $delivered, int $read, int $failed, string $dbStatus = ''): string
    {
        // Menunggu — belum ada penerima
        if ($total === 0) {
            return '<span class="bc-badge bc-menunggu">'
                 . '<i class="bx bx-time me-1"></i>Menunggu</span>';
        }

        // Sedang processing (baru mulai kirim)
        if ($dbStatus === 'processing' && $delivered === 0 && $read === 0) {
            return '<span class="bc-badge bc-berjalan">'
                 . '<i class="bx bx-loader-alt bx-spin me-1"></i>Sedang Kirim</span>';
        }

        $s = self::normalize($total, $delivered, $read, $failed);

        // Bar percentages
        $delivPct = $total > 0 ? round($s['delivered'] / $total * 100) : 0;
        $readPct  = $total > 0 ? round($s['read']      / $total * 100) : 0;
        $failPct  = $total > 0 ? round($s['failed']    / $total * 100) : 0;

        // Numbers row
        $parts = [];
        if ($s['delivered'] > 0) $parts[] = number_format($s['delivered']) . ' delivered';
        if ($s['read'] > 0)      $parts[] = number_format($s['read'])      . ' dibaca';
        if ($s['failed'] > 0)    $parts[] = number_format($s['failed'])    . ' gagal';
        if ($s['pending'] > 0)   $parts[] = number_format($s['pending'])   . ' menunggu';
        $numsHtml = implode(' · ', $parts);

        $cls = 'bc-' . $s['status']; // bc-selesai | bc-berjalan | bc-gagal | bc-menunggu

        return '<div class="bc-status">'
             . '<div class="bc-status-head">'
             . '<span class="bc-badge ' . $cls . '">' . e($s['label']) . '</span>'
             . '<span class="bc-rate">' . $s['deliveryRate'] . '% delivery</span>'
             . '</div>'
             . '<div class="bc-bar">'
             . '<div style="width:' . $delivPct . '%;background:#16A34A;height:100%"></div>'
             . '<div style="width:' . $readPct  . '%;background:#2E8DE1;height:100%"></div>'
             . '<div style="width:' . $failPct  . '%;background:#DC2626;height:100%"></div>'
             . '</div>'
             . '<div class="bc-nums">' . $numsHtml . '</div>'
             . '</div>';
    }
}
