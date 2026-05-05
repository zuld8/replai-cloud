<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Observers\Blash\BlashWhatsappObserver;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $blastWhatsappObserver;

    public function __construct(BlashWhatsappObserver $blashWhatsappObserver)
    {
        $this->blastWhatsappObserver        = $blashWhatsappObserver;
    }


    public function index(Request $request)
    {
        $devices        = $this->blastWhatsappObserver->getStatisticData($request);
        return view('logs.statistic', ['page'   => __('report.send_statistics.report_title'), 'breadcumb' => true], compact('devices'));
    }
}
