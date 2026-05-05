<?php

namespace App\Http\Controllers\Broadcast;

use App\Http\Controllers\Controller;
use App\Models\MetaAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BroadcastWabaController extends Controller
{
    /**
     * List all WABA broadcast campaigns
     * User selects which MetaAccount via dropdown — no meta in URL
     */
    public function index(Request $request)
    {
        // MetaAccount uses global scope via booted() → auto-filters by session business_id
        $wabaAccounts = MetaAccount::orderBy('name')
            ->get(['id', 'name', 'details']);

        $defaultMeta = $wabaAccounts->first();

        return view('broadcast.waba.index', [
            'page'         => 'Broadcast WhatsApp Business API',
            'breadcumb'    => true,
            'wabaAccounts' => $wabaAccounts,
            'defaultMeta'  => $defaultMeta,
        ]);
    }

    /**
     * Server-side DataTable endpoint
     * Receives ?meta_id=xxx from JS
     */
    public function listData(Request $request)
    {
        $metaId = $request->meta_id;

        // Verify meta belongs to user's business via global scope
        $meta = MetaAccount::findOrFail($metaId);
        $businessId = $meta->business_id;

        $query = DB::table('blash_whatsapps as bw')
            ->leftJoin('categories as c',         'c.id', '=', 'bw.category_id')
            ->leftJoin('message_templates as t',  't.id', '=', 'bw.template_id')
            ->where('bw.business_id', $businessId)
            ->where('bw.meta_account_id', $metaId)
            ->where('bw.use', 'whatsapp')
            ->where('bw.waba', 'yes')
            ->select([
                'bw.id',
                'bw.name',
                'bw.schedule',
                'bw.status',
                'bw.created_at',
                DB::raw('COALESCE(c.name, "-") as category_name'),
                DB::raw('COALESCE(t.name, "-") as template_name'),
                // ✅ Use cached stat columns — ~90% faster than correlated subqueries
                'bw.stat_total as total_recipients',
                'bw.stat_sent as total_sent',
                'bw.stat_failed as total_failed',
                'bw.stat_delivered as total_delivered',
                'bw.stat_read as total_read',
                'bw.stat_delivery_failed as total_delivery_failed',
            ])
            ->orderBy('bw.created_at', 'desc');

        // Status filter
        if ($request->status_filter === 'done') {
            $query->whereIn('bw.status', ['success', 'partial_success', 'failed']);
        } elseif ($request->status_filter === 'pending') {
            $query->whereRaw('(SELECT COUNT(*) FROM blash_details WHERE blash_whatsapp_id = bw.id) = 0');
        } elseif ($request->status_filter === 'partial_success') {
            $query->where('bw.status', 'partial_success');
        } elseif ($request->status_filter === 'failed') {
            $query->where('bw.status', 'failed');
        } elseif ($request->status_filter === 'processing') {
            $query->where('bw.status', 'processing');
        }

        // Month filter
        if ($request->month_filter) {
            $query->whereMonth('bw.created_at', now()->month)
                  ->whereYear('bw.created_at', now()->year);
        }

        return DataTables::of($query)
            ->addColumn('title_col', function ($row) {
                return '<div class="d-flex flex-column">
                    <span class="fw-600 text-dark">' . e($row->name) . '</span>
                    <small class="text-muted">' . \Carbon\Carbon::parse($row->created_at)->diffForHumans() . '</small>
                </div>';
            })
            ->addColumn('schedule_col', function ($row) {
                $dt = \Carbon\Carbon::parse($row->schedule);
                return '<div class="d-flex flex-column">
                    <span class="' . ($dt->isPast() ? 'text-muted' : 'text-primary fw-500') . '">'
                    . $dt->format('d M Y') . '</span>
                    <small class="text-secondary">' . $dt->format('H:i') . ' WIB</small>
                </div>';
            })
            ->addColumn('stats_col', function ($row) {
                $total  = (int) $row->total_recipients;
                $sent   = (int) $row->total_sent;
                $failed = (int) ($row->total_failed ?? 0);
                $delivered = (int) ($row->total_delivered ?? 0);
                $read   = (int) ($row->total_read ?? 0);
                $deliveryFailed = (int) ($row->total_delivery_failed ?? 0);

                if ($total === 0) {
                    return '<span class="badge bg-secondary-transparent text-secondary rounded-pill px-3" style="background:rgba(100,116,139,0.12)!important;">
                        <i class="bx bx-time me-1"></i>Menunggu
                    </span>';
                }

                // Use delivery stats for more accurate percentage
                $realDelivered = $delivered + $read;
                $realFailed = $failed + $deliveryFailed;
                $pct = round($sent / $total * 100);
                $deliveryPct = $total > 0 ? round($realDelivered / $total * 100) : 0;

                // Use DB status field as primary source of truth
                $dbStatus = $row->status ?? '';
                if ($dbStatus === 'partial_success') {
                    $color = '#d97706'; $label = 'Sebagian'; $icon = 'bxs-error-circle'; $bg = 'rgba(245,158,11,0.15)'; $tc = '#d97706';
                } elseif ($dbStatus === 'failed') {
                    $color = '#dc2626'; $label = 'Gagal';    $icon = 'bx-x-circle';      $bg = 'rgba(239,68,68,0.12)';  $tc = '#dc2626';
                } elseif ($dbStatus === 'success') {
                    $color = '#059669'; $label = 'Selesai';  $icon = 'bx-check-circle';  $bg = 'rgba(16,185,129,0.12)'; $tc = '#059669';
                } elseif ($dbStatus === 'processing') {
                    $color = '#0284c7'; $label = 'Proses';   $icon = 'bx-loader-alt';    $bg = 'rgba(2,132,199,0.12)';  $tc = '#0284c7';
                } elseif ($realFailed > 0 && $realFailed > $realDelivered) {
                    $color = '#dc2626'; $label = 'Gagal';    $icon = 'bx-x-circle';     $bg = 'rgba(239,68,68,0.12)';   $tc = '#dc2626';
                } elseif ($pct >= 100 && $deliveryPct >= 50) {
                    $color = '#059669'; $label = 'Selesai';  $icon = 'bx-check-circle'; $bg = 'rgba(16,185,129,0.12)'; $tc = '#059669';
                } elseif ($pct >= 50) {
                    $color = '#d97706'; $label = 'Sebagian'; $icon = 'bxs-hourglass';   $bg = 'rgba(245,158,11,0.12)';  $tc = '#d97706';
                } else {
                    $color = '#dc2626'; $label = 'Gagal';    $icon = 'bx-x-circle';     $bg = 'rgba(239,68,68,0.12)';   $tc = '#dc2626';
                }

                $barFill = min(100, $pct);

                // Show delivery detail
                $deliveryInfo = '';
                if ($realDelivered > 0 || $realFailed > 0) {
                    $parts = [];
                    if ($delivered > 0) $parts[] = $delivered . ' delivered';
                    if ($read > 0) $parts[] = $read . ' dibaca';
                    if ($deliveryFailed > 0) $parts[] = $deliveryFailed . ' gagal';
                    $deliveryInfo = '<small class="text-muted" style="font-size:0.65rem;">' . implode(' · ', $parts) . '</small>';
                }

                return '<div class="d-flex flex-column gap-1">
                    <span class="badge rounded-pill px-2 py-1" style="background:' . $bg . ';color:' . $tc . ';font-size:0.72rem;">
                        <i class="bx ' . $icon . ' me-1"></i>' . $label . ' ' . $pct . '%
                    </span>
                    <small class="text-muted" style="font-size:0.7rem;">' . number_format($sent) . '/' . number_format($total) . ' terkirim</small>
                    ' . $deliveryInfo . '
                    <div style="height:4px;border-radius:2px;background:#e5e7eb;overflow:hidden;">
                        <div style="height:100%;width:' . $barFill . '%;background:' . $color . ';border-radius:2px;transition:width 0.4s;"></div>
                    </div>
                </div>';
            })
            ->addColumn('action_col', function ($row) use ($meta) {
                $editUrl   = route('waba.broadcast.update', [$meta->id, $row->id]);
                $detailUrl = route('waba.broadcast.detail', [$meta->id, $row->id]);
                $deleteUrl = route('blash.delete', $row->id);
                return '<div class="d-flex gap-1">
                    <a href="' . $editUrl . '" class="btn btn-sm btn-outline-warning btn-icon" title="Edit">
                        <i class="bx bx-pencil"></i>
                    </a>
                    <a href="' . $detailUrl . '" class="btn btn-sm btn-outline-info btn-icon" title="Detail">
                        <i class="bx bx-detail"></i>
                    </a>
                    <a href="' . $deleteUrl . '" class="btn btn-sm btn-outline-danger btn-icon deletebutton" title="Hapus">
                        <i class="bx bx-trash"></i>
                    </a>
                </div>';
            })
            ->rawColumns(['title_col', 'schedule_col', 'stats_col', 'action_col'])
            ->with([
                'meta_name'  => $meta->name,
                'meta_phone' => $meta->phone,
            ])
            ->make(true);
    }
    /**
     * Retry failed recipients in a broadcast (no duplicates)
     */
    public function retryFailed(Request $request, $metaId, $broadcastId)
    {
        $meta = MetaAccount::findOrFail($metaId);
        
        $broadcast = DB::table('blash_whatsapps')
            ->where('id', $broadcastId)
            ->where('business_id', $meta->business_id)
            ->first();

        if (!$broadcast) {
            return back()->with('gagal', 'Broadcast tidak ditemukan');
        }

        // Count failed
        $failedCount = DB::table('blash_details')
            ->where('blash_whatsapp_id', $broadcastId)
            ->where('sending_status', 'no')
            ->count();

        if ($failedCount == 0) {
            return back()->with('gagal', 'Tidak ada pesan gagal untuk diulang');
        }

        // Call artisan command
        \Artisan::call('broadcast:retry-failed', ['broadcast_id' => $broadcastId]);

        return back()->with('sukses', "Berhasil mengirim ulang {$failedCount} pesan gagal. Pesan yang sudah terkirim tidak akan diulang.");
    }

}
