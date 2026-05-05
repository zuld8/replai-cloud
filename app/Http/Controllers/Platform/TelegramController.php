<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Master\MessageTemplate;
use App\Models\Setting;
use App\Models\TelegramKey;
use App\Observers\ChatBot\FineTunnelObserver;
use App\Observers\Master\TemplateObserver;
use App\Observers\UserObserver;
use App\Services\Platform\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TelegramController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Telegram Controllers
    |--------------------------------------------------------------------------
    */

    protected $telegramService;
    protected $fineTunnelObserver;
    protected $templateObserver;
    protected $usersObserver;

    public function __construct(TelegramService $telegramService, FineTunnelObserver $fineTunnelObserver, TemplateObserver $templateObserver, UserObserver $userObserver)
    {
        $this->telegramService          = $telegramService;
        $this->fineTunnelObserver       = $fineTunnelObserver;
        $this->templateObserver         = $templateObserver;
        $this->usersObserver            = $userObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Telegram List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $summary    = [
            'all'           => $this->telegramService->getData($request)->count(),
            'active'        => $this->telegramService->getData($request)->where('status', 'active')->count(),
            'not_active'    => $this->telegramService->getData($request)->where('status', 'no_active')->count(),
        ];

        $telegram     = $this->telegramService->getData($request)->where(function ($q) use ($request) {
            return $request->status ? $q->where("status", $request->status) : '';
        })->get();
        return view('telegram.index', ['page'    => __('platform.telegram.integrated_telegram_list'), 'breadcumb' => true], compact('telegram', 'summary'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Page
    |--------------------------------------------------------------------------
    */

    public function create(Request $request)
    {
        $users          = $this->usersObserver->getData($request)->get(['id', 'name']);
        $fineTunnels    = $this->fineTunnelObserver->getData($request)->get(['name', 'id']);
        return view('telegram.create', ['page'   => __('platform.telegram.add_telegram'), 'breadcumb' => true], compact('fineTunnels', 'users'));
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Update Page
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, TelegramKey $telegram)
    {
        $users          = $this->usersObserver->getData($request)->get(['id', 'name']);
        $fineTunnels    = $this->fineTunnelObserver->getData($request)->get(['name', 'id']);
        return view('telegram.update', ['page'   => __('platform.telegram.edit_telegram'), 'breadcumb' => true], compact('fineTunnels', 'telegram', 'users'));
    }


    /*
    |--------------------------------------------------------------------------
    | 5. Delete Telegram
    |--------------------------------------------------------------------------
    */

    public function delete(TelegramKey $telegram)
    {

        
        // Set Webhook
        $removeWebhook = $this->telegramService->removeWebhook($telegram);
        if ($removeWebhook->status() != 200) {
            $resultData    = json_decode($removeWebhook->body());
            return redirect()->back()->with(['gagal'   => $resultData->description]);
        }

        $this->telegramService->deleteData($telegram);
        return redirect()->back()->with(['flash'    => __('general.success_deleted')]);
    }


    /*
    |--------------------------------------------------------------------------
    | 6. Store Data to Database
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'                  => 'required',
            'token'                 => 'required',
            'daily_limit'           => 'required|in:yes,no',
            'limit'                 => 'required_if:daily_limit,yes',
            'certain_day'           => 'required|in:yes,no',
            'days'                  => 'required_if:certain_day,yes',
            'certain_time'          => 'required|in:yes,no',
            'start_time'            => 'required_if:certain_time,yes',
            'end_time'              => 'required_if:certain_time,yes',
            'method'                => 'required|in:ai,chatbot,all',
            'tunnel'                => 'required_if:method,ai,all',
            'agent'                 => 'required|array'
        ]);

        $validationCheck = $this->telegramService->checkLimit();

        if ($validationCheck == false) {
            return redirect()->back()->with(['gagal'    => __('validation.telegram_limit')]);
        }

        try {
            DB::beginTransaction();

            $telegram   = $this->telegramService->createData($request);

            // Set Webhook
            $webhookSet = $this->telegramService->setWebhook($telegram);
            if ($webhookSet->status() != 200) {
                DB::rollBack();
                $resultData    = json_decode($webhookSet->body());
                return redirect()->back()->with(['gagal'   => $resultData->description]);
            }

            DB::commit();

            return redirect()->route('telegrams')->with(['flash' => __('general.success_add_data')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(['gagal' => $e->getMessage()]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 7. Update Data to Database
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, TelegramKey $telegram)
    {
        $this->validate($request, [
            'name'                  => 'required',
            'token'                 => 'required',
            'daily_limit'           => 'required|in:yes,no',
            'limit'                 => 'required_if:daily_limit,yes',
            'certain_day'           => 'required|in:yes,no',
            'days'                  => 'required_if:certain_day,yes',
            'certain_time'          => 'required|in:yes,no',
            'start_time'            => 'required_if:certain_time,yes',
            'end_time'              => 'required_if:certain_time,yes',
            'method'                => 'required|in:ai,chatbot,all',
            'tunnel'                => 'required_if:method,ai,all',
            'agent'                 => 'required|array'
        ]);



        try {
            DB::beginTransaction();

            $this->telegramService->updateData($request, $telegram);

            // Set Webhook
            $webhookSet = $this->telegramService->setWebhook($telegram);
            if ($webhookSet->status() != 200) {
                DB::rollBack();
                $resultData    = json_decode($webhookSet->body());
                return redirect()->back()->with(['gagal'   => $resultData->description]);
            }

            DB::commit();

            return redirect()->route('telegrams')->with(['flash' => __('general.success_update')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(['gagal' => $e->getMessage()]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 13.Test Send Message
    |--------------------------------------------------------------------------
    */

    public function testSend(Request $request)
    {
        $telegrams        = $this->telegramService->getData($request)->where('status', 'active')->get(['id', 'name', 'phone']);
        $templates      = $this->templateObserver->getData($request)->where('for_waba', 'no')->where('type', 'whatsapp')->get(['id', 'name']);
        return view('telegram.send_test', ['page'    => 'Kirim Pesan Single', 'breadcumb' => true], compact('telegrams', 'templates'));
    }


    /*
    |--------------------------------------------------------------------------
    | 14.Single Message Send
    |--------------------------------------------------------------------------
    */

    public function singleSend(Request $request)
    {
        $this->validate($request, [
            'telegram'            => 'required',
            'template'          => 'required',
            'type'              => 'required',
            'phone'             => 'required'
        ]);

        $settings       = Setting::first(['phone_country_code']);
        $phone          = $request->phone;

        if (substr($request->phone, 0, 1) == 0) {
            $phone      = $settings->phone_country_code . substr($request->phone, 1, 15);
        }

        $template       = MessageTemplate::find($request->template);
        $this->whatsappServiceObserver->sendMessage($phone, $request->telegram, $template->message, $template->media_data ?? '', $template->type_content, json_decode($template->button_or_list, true), $request->type == 'group' ? true : false);

        return redirect()->back()->with(['flash'    => 'Success Send Message']);
    }
}
