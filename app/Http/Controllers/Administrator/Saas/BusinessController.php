<?php

namespace App\Http\Controllers\Administrator\Saas;

use App\Http\Controllers\Controller;
use App\Models\Blash\BlashDetail;
use App\Models\ChatBot\FineTunnel;
use App\Models\ChatBot\HistoryChat;
use App\Models\LiveChat;
use App\Models\Master\Category;
use App\Models\Setting;
use App\Models\Store\Store;
use App\Models\User;
use App\Models\WhatsappDevice;
use App\Models\WhatsappKeyAccount;
use App\Observers\Blash\LogObserver;
use App\Observers\Saas\BusinessObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BusinessController extends Controller
{
    protected $businessObserver;
    protected $logsObserver;

    public function __construct(BusinessObserver $businessObserver, LogObserver $logObserver)
    {
        $this->businessObserver     = $businessObserver;
        $this->logsObserver         = $logObserver;
    }

    public function index(Request $request)
    {
        $businesses      = $this->businessObserver->getForAdmin($request)->paginate(12);

        $pagination       = array(
            'current_page'      => $businesses->currentPage(),
            'to_page'           => $businesses->lastPage(),
            'per_page'          => $businesses->perPage(),
            'first_item'        => $businesses->firstItem(),
            'last_item'         => $businesses->lastItem(),
            'links'             => $businesses->linkCollection()->toArray()
        );

        return view('admin.business.index', ['page'     => __('business.business_list'), 'breadcumb' => false], compact('businesses', 'pagination'));
    }

    public function getByJquery(Request $request)
    {
        $merchants      = $this->businessObserver->getData($request)->limit(20)->get(['id', 'name']);
        return response()->json($merchants);
    }

    public function detail(Request $request, $business)
    {

        $business   = Setting::withoutGlobalScopes()->find($business);
        $summary    = [
            'unofficial'        => WhatsappDevice::withoutGlobalScopes()->where('business_id', $business->id)->count(),
            'official'          => WhatsappKeyAccount::withoutGlobalScopes()->where('business_id', $business->id)->count(),
            'livechats'         => LiveChat::withoutGlobalScopes()->where('business_id', $business->id)->count(),
            'finetunnels'       => FineTunnel::withoutGlobalScopes()->where('business_id', $business->id)->count(),
            'stores'            => Store::withoutGlobalScopes()->where('business_id', $business->id)->count(),
            'categories'        => Category::withoutGlobalScopes()->where('business_id', $business->id)->count(),
            'user'              => User::withoutGlobalScopes()->where('merchant_id', $business->merchant_id)->count(),
            'blast_w'           => BlashDetail::whereHas('parent', function ($q) use ($business) {
                return $q->where('use', 'whatsapp')->where('business_id', $business->id);
            })->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count(),

            'blast_e'           => BlashDetail::whereHas('parent', function ($q) use ($business) {
                return $q->where('use', 'email')->where('business_id', $business->id);
            })->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count(),

            'scraping'          => Store::withoutGlobalScopes()->where('business_id', $business->id)->where('scrapping_id', '!=', null)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count(),
            'sending'       => BlashDetail::whereHas('parent', function ($q) use ($business) {
                return $q->where("business_id", $business->id);
            })->where("reports", null)->where('created_at', ">=", now()->subDays(30))->count(),
            'not_sending'   => BlashDetail::whereHas('parent', function ($q) use ($business) {
                return $q->where("business_id", $business->id);
            })->where("reports", "!=", null)->where('created_at', ">=", now()->subDays(30))->count(),
        ];

        $interactions = [
            'open'              => HistoryChat::withoutGlobalScopes()->where('business_id', $business->id)->where('status', 'open')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count(),
            'pending'           => HistoryChat::withoutGlobalScopes()->where('business_id', $business->id)->where('status', 'pending')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count(),
            'assign'            => HistoryChat::withoutGlobalScopes()->where('business_id', $business->id)->where('handled_by', '!=', null)->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count(),
            'resolved'          => HistoryChat::withoutGlobalScopes()->where('business_id', $business->id)->where('status', 'resolved')->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count(),
        ];

        return view('admin.business.detail', ['page'  => 'Detail Bisnis - ' . $business->name, 'breadcumb'   => false], compact('summary', 'interactions'));
    }

    public function interactionAnalysis($business)
    {
        $business   = Setting::withoutGlobalScopes()->find($business);
        $interactions = HistoryChat::withoutGlobalScopes()->where('business_id', $business->id)->selectRaw("
            DATE(created_at) as date, COUNT(*) as count
        ")
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($interactions);
    }

    public function deleteBusiness($business)
    {

        $business   = Setting::withoutGlobalScopes()->find($business);

        try {

            DB::beginTransaction();

            $business->delete();

            DB::commit();

            return redirect()->back()->with(['flash'   => 'Data bisnis berhasil di hapus']);
        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()->back()->with(['gagal'    => $e->getMessage()]);
        }
    }
}
