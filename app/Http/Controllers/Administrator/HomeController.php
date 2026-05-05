<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Blash\BlashDetail;
use App\Models\ChatBot\FineTunnel;
use App\Models\ChatBot\HistoryChatDetail;
use App\Models\Master\Category;
use App\Models\Merchant\Merchant;
use App\Models\Package\PackageTransaction;
use App\Models\Setting;
use App\Models\Store\Store;
use App\Models\User;
use App\Models\WhatsappDevice;
use App\Observers\Blash\LogObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{

    protected $logsObserver;
    public function __construct(LogObserver $logObserver)
    {
        $this->middleware('auth');
        $this->logsObserver     = $logObserver;
    }

    public function home(Request $request)
    {
        $monthYear = date('Y-m');

        // OPTIMIZED: Cache summary for 15 minutes (admin data doesn't need real-time)
        $summary = Cache::remember("admin_summary_{$monthYear}", 900, function () {
            $monthStart = now()->startOfMonth()->toDateString();
            $monthEnd   = now()->endOfMonth()->toDateString();

            return [
                'merchants'   => Merchant::count(),
                'business'    => Setting::where('merchant_id', '!=', null)->count(),
                'packages'    => PackageTransaction::where('status', 'success')->where('type', 'package')->whereBetween('created_at', [$monthStart, $monthEnd])->sum('final_total'),
                'topup'       => PackageTransaction::where('status', 'success')->where('type', 'topup')->whereBetween('created_at', [$monthStart, $monthEnd])->sum('final_total'),
                'finetunnels' => FineTunnel::withoutGlobalScopes()->count(),
                'users'       => User::withoutGlobalScopes()->count(),
                'devices'     => WhatsappDevice::withoutGlobalScopes()->count(),
                'blast_w'     => BlashDetail::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'blast_e'     => BlashDetail::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'scraping'    => Store::where('scrapping_id', '!=', null)->whereBetween('created_at', [$monthStart, $monthEnd])->count(),
            ];
        });

        // OPTIMIZED: Cache data counts for 15 minutes
        $data = Cache::remember("admin_data_{$monthYear}", 900, function () {
            $thirtyDaysAgo = now()->subDays(30)->toDateTimeString();

            return [
                'stores'      => Store::count(),
                'categories'  => Category::count(),
                'blashs'      => BlashDetail::where('created_at', '>=', $thirtyDaysAgo)->where('reports', null)->count(),
                'scrapp'      => Store::where('scrapping_id', '!=', null)->where('created_at', '>=', $thirtyDaysAgo)->count(),
                'sending'     => BlashDetail::where('reports', null)->where('created_at', '>=', $thirtyDaysAgo)->count(),
                'not_sending' => BlashDetail::where('reports', '!=', null)->where('created_at', '>=', $thirtyDaysAgo)->count(),
            ];
        });

        // These are small queries (limit 10) — no need to cache
        $mustFollow = PackageTransaction::select('business_id', 'package_id', DB::raw('MAX(expire_date) as last_expire_date'))
            ->where("business_id", "!=", null)
            ->where('type', 'package')
            ->where("status", "success")
            ->groupBy('business_id', 'package_id')
            ->havingRaw('MAX(expire_date) <= ?', [now()->addDays(7)->toDateString()])
            ->havingRaw('MAX(expire_date) >= ?', [now()->toDateString()])
            ->limit(10)
            ->get();

        $merchantNotPackage = Setting::withoutGlobalScopes()
            ->where('merchant_id', '!=', null)
            ->where('created_at', '>', now()->subDays(30)->endOfDay())
            ->whereDoesntHave('transaction', function ($query) {
                $query->where('type', 'package')
                    ->where('status', 'success');
            })
            ->limit(10)
            ->get();

        $notPayment = PackageTransaction::where("status", "pending")->where('created_at', '>', now()->subDays(7)->endOfDay())->orderBy("created_at", "desc")->limit(10)->get();
        $merchants  = Merchant::where('created_at', '>', now()->subDays(7)->endOfDay())->limit(10)->get();

        // OPTIMIZED: Cache logs for 2 minutes
        $logs = Cache::remember("admin_logs_{$monthYear}", 120, function () use ($request) {
            return [
                'email'    => $this->logsObserver->getData($request, 'email')->limit(10)->get(['description', 'error', 'type', 'status', 'created_at']),
                'whatsapp' => $this->logsObserver->getData($request, 'whatsapp')->limit(10)->get(['description', 'error', 'type', 'status', 'created_at']),
                'scrapp'   => $this->logsObserver->getData($request, 'scrapping')->limit(10)->get(['description', 'error', 'type', 'status', 'created_at']),
            ];
        });

        return view('admin.home', ['page'  => __('page.dashboard'), 'breadcumb' => false], compact('data', 'logs', 'summary', 'mustFollow', 'merchantNotPackage', 'notPayment', 'merchants'));
    }

    public function creditAiResponse()
    {
        $data = \Cache::remember("admin_credit_ai_" . date("Y-m"), 900, function () {
            return \App\Models\ChatBot\HistoryChatDetail::selectRaw("
                DATE(created_at) as date, sum(credit_using) as count
            ")
                ->whereBetween("created_at", [\Carbon\Carbon::now()->startOfMonth(), \Carbon\Carbon::now()->endOfMonth()])
                ->groupBy("date")
                ->orderBy("date")
                ->get();
        });

        return response()->json($data);
    }

    public function analiss(Request $request)
    {
        $senderData     = array();
        $notSenderData  = array();
        $dateData       = array();

        $blashData = BlashDetail::selectRaw('LEFT(created_at, 10) as date, 
        SUM(CASE WHEN reports IS NULL THEN 1 ELSE 0 END) AS sending,
        SUM(CASE WHEN reports IS NOT NULL THEN 1 ELSE 0 END) AS not_sending')
            ->where('created_at', ">=", now()->subDays(30))
            ->groupBy('date')
            ->get();

        foreach ($blashData as $blash) {
            $dateData[]             = Carbon::parse($blash->date, 'Asia/Jakarta')->setTimezone('Asia/Jakarta')->format('d, M Y');
            $senderData[]           = (int)$blash->sending;
            $notSenderData[]        = (int)$blash->not_sending;
        }

        return response()->json([
            'analisis_blash'    => array(
                'sender'            => $senderData,
                'not_sender'        => $notSenderData,
                'date'              => $dateData,
            ),
        ]);
    }
}
