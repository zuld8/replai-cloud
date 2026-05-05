<?php

namespace App\Http\Controllers\Broadcast;

use App\Http\Controllers\Controller;
use App\Models\MetaAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TemplateWabaController extends Controller
{
    public function index(Request $request)
    {
        $businessId = session('business_id') ?? auth()->user()->business_id ?? '';

        $wabaAccounts = DB::table('meta_accounts as m')
            ->leftJoin('whatsapp_key_accounts as w', 'w.meta_account_id', '=', 'm.id')
            ->whereNull('m.deleted_at')
            ->where('m.business_id', $businessId)
            ->select(['m.id', 'm.name', 'm.business_app', 'w.phone', 'w.status as device_status'])
            ->orderBy('m.name')
            ->get();

        $defaultMeta = $wabaAccounts->first();

        return view('broadcast.waba.template-picker', [
            'page'         => 'Template WhatsApp Business API',
            'breadcumb'    => true,
            'wabaAccounts' => $wabaAccounts,
            'defaultMeta'  => $defaultMeta,
        ]);
    }

    public function listData(Request $request)
    {
        $metaId     = $request->meta_id;
        $businessId = session('business_id') ?? auth()->user()->business_id ?? '';

        $query = DB::table('message_templates as t')
            ->where('t.meta_account_id', $metaId)
            ->where('t.business_id', $businessId)
            ->where('t.type', 'whatsapp')
            ->where('t.for_waba', 'yes')
            ->whereNull('t.deleted_at')
            ->select(['t.id', 't.name', 't.category', 't.lang', 't.type_content', 't.waba_status_template', 't.quality_score', 't.created_at', 't.image'])
            ->orderByDesc('t.created_at');

        // Status filter
        if ($request->status_filter) {
            $query->whereRaw('UPPER(t.waba_status_template) = ?', [strtoupper($request->status_filter)]);
        }

        return DataTables::of($query)
            ->addColumn('name_col', function ($row) {
                $img = '';
                if ($row->image) {
                    // Use &apos; to avoid quote conflict
                    $img = '<img src="' . asset('storage/' . $row->image) . '" class="rounded me-2" style="width:36px;height:36px;object-fit:cover;" onerror="this.style.display=&apos;none&apos;">';
                }
                return '<div class="d-flex align-items-center">' . $img
                    . '<div><div class="fw-600 text-dark">' . e($row->name) . '</div>'
                    . '<small class="text-muted">' . strtoupper($row->type_content ?? 'TEXT') . '</small></div></div>';
            })
            ->addColumn('status_col', function ($row) {
                $s = strtoupper($row->waba_status_template ?? 'PENDING');
                $map = [
                    'APPROVED'  => ['bg-success-transparent text-success', 'bx-check-circle',  'Disetujui'],
                    'REJECTED'  => ['bg-danger-transparent text-danger',   'bx-x-circle',     'Ditolak'],
                    'PENDING'   => ['bg-warning-transparent text-warning',  'bx-time',         'Menunggu'],
                    'IN_APPEAL' => ['bg-info-transparent text-info',       'bx-error-circle', 'Banding'],
                    'PAUSED'    => ['bg-secondary-transparent text-secondary','bx-pause-circle','Dijeda'],
                ];
                [$cls, $icon, $lbl] = $map[$s] ?? ['bg-secondary-transparent text-secondary', 'bx-question-mark', $s];
                return '<span class="badge ' . $cls . ' px-3 py-1 rounded-pill"><i class="bx ' . $icon . ' me-1"></i>' . $lbl . '</span>';
            })
            ->addColumn('quality_col', function ($row) {
                $q = strtoupper($row->quality_score ?? 'UNKNOWN');
                $map = [
                    'GREEN'   => ['text-success', 'Tinggi'],
                    'YELLOW'  => ['text-warning', 'Sedang'],
                    'RED'     => ['text-danger',  'Rendah'],
                ];
                [$cls, $lbl] = $map[$q] ?? ['text-muted', $q];
                return '<span class="' . $cls . ' fw-500">' . $lbl . '</span>';
            })
            ->addColumn('action_col', function ($row) {
                $metaId  = request()->meta_id;
                $editUrl = route('waba.template.update', [$metaId, $row->id]);
                $delUrl  = route('waba.template.delete',  [$metaId, $row->id]);
                $bcUrl   = route('waba.broadcast.create', $metaId) . '?template_id=' . $row->id;
                return '<div class="d-flex gap-1">
                    <a href="' . $editUrl . '" class="btn btn-sm btn-outline-warning btn-icon" title="Edit"><i class="bx bx-pencil"></i></a>
                    <a href="' . $bcUrl   . '" class="btn btn-sm btn-outline-success btn-icon" title="Buat Broadcast"><i class="bx bx-broadcast"></i></a>
                    <a href="' . $delUrl  . '" class="btn btn-sm btn-outline-danger btn-icon deletebutton" title="Hapus"><i class="bx bx-trash"></i></a>
                </div>';
            })
            ->rawColumns(['name_col', 'status_col', 'quality_col', 'action_col'])
            ->make(true);
    }
}
