<?php

namespace App\Http\Controllers;

use App\Exports\WhatsappLogExport;
use App\Observers\Blash\LogObserver;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LogController extends Controller
{



    protected $logObserver;

    public function __construct(LogObserver $logObserver)
    {
        $this->logObserver      = $logObserver;
    }

    public function whatsapp(Request $request)
    {

        if ($request->ajax()) {

            $blashs         = $this->logObserver->getData($request, 'whatsapp');

            return DataTables::of($blashs)
                ->addColumn('device', function ($row) {
                    return $row->device->name ?? '';
                })->addColumn('user', function ($row) {
                    return $row->store->name ?? '';
                })->addColumn('phone', function ($row) {
                    return $row->store->phone ?? '';
                })->addColumn('text_pesan', function ($row) {
                    return substr($row->text, 0, 200);
                })->addColumn('status_attribute', function ($row) {
                    $html = '';
                    if ($row->status == 'pending') {
                        $html = '<span class="badge bg-warning text-red-fg">Pending</span>';
                    }
                    if ($row->status == 'success') {
                        $html = '<span class="badge bg-success text-lime-fg">Terkirim</span>';
                    }
                    if ($row->status == 'error') {
                        $html = '<span class="badge bg-red text-red-fg">Gagal</span>';
                    }

                    return $html;
                })->rawColumns(['status_attribute'])
                ->make(true);
        }

        return view('logs.whatsapp', ['page' => __('report.whatsapp_log.title'), 'type'  => 'whatsapp', 'breadcumb' => true]);
    }

    public function scrapping(Request $request)
    {
        $logs   = $this->logObserver->getData($request, 'scrapping')->get(['description', 'error', 'type', 'status', 'created_at']);
        return view('logs.index', ['page' => __('report.scrapping_log.title'), 'type' => 'scrapping', 'breadcumb' => true], compact('logs'));
    }

    public function email(Request $request)
    {
        $logs   = $this->logObserver->getData($request, 'email')->get(['description', 'error', 'type', 'status', 'created_at']);
        return view('logs.index', ['page' => __('report.email_log.title'), 'type' => 'email', 'breadcumb' => true], compact('logs'));
    }

    public function delete(Request $request)
    {
        $this->logObserver->getData($request, $request->type ?? 'whatsapp')->delete();
        return redirect()->back()->with(['flash'    => __('general.success_deleted')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 8. Export Data
    |--------------------------------------------------------------------------
    */

    public function export(Request $request)
    {
        return (new WhatsappLogExport($request, $this->logObserver))->download('whatsapp-log-sender-' . date('Y-m-d') . '.xlsx');
    }
}
