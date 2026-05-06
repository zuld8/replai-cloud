<?php

namespace App\Http\Controllers\ChatBot;

use App\Http\Controllers\Controller;
use App\Imports\Automation\ChatBotImport;
use App\Models\ChatBot\ChatBot;
use App\Models\ChatBot\ChatBotImage;
use App\Models\Master\MessageTemplate;
use App\Models\WhatsappDevice;
use App\Observers\ChatBot\ChatBotObserver;
use App\Observers\LiveChatObserver;
use App\Observers\Master\TemplateObserver;
use App\Observers\WhatsappDeviceObserver;
use App\Services\Platform\InstagramService;
use App\Services\Platform\MesaangerService;
use App\Services\Platform\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ChatBotController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | ChatBot Auto Reply Controllers
    |--------------------------------------------------------------------------
    */

    protected $chatBotObserver;
    protected $templateObserver;
    protected $deviceObserver;
    protected $livechatObserver;
    protected $telegramService;
    protected $instagramService;
    protected $messengerService;

    public function __construct(
        ChatBotObserver $chatBotObserver,
        TemplateObserver $templateObserver,
        WhatsappDeviceObserver $deviceObserver,
        LiveChatObserver $liveChatObserver,
        TelegramService $telegramService,
        InstagramService $instagramService,
        MesaangerService $messengerService
    ) {
        $this->chatBotObserver      = $chatBotObserver;
        $this->templateObserver     = $templateObserver;
        $this->deviceObserver       = $deviceObserver;
        $this->livechatObserver     = $liveChatObserver;
        $this->telegramService      = $telegramService;
        $this->instagramService     = $instagramService;
        $this->messengerService     = $messengerService;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. ChatBot Auto Reply List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $queryArray     = $request->all();
        $params         = http_build_query($queryArray);

        if ($request->ajax()) {

            $stores         = $this->chatBotObserver->getData($request)->where('meta_account_id', null);

            return DataTables::of($stores)
                ->addColumn('method', function ($row) {
                    return $row->reply_method == 'text' ? 'Text' : ($row->reply_method == 'image' ? 'Image' : 'Template');
                })->addColumn('template', function ($row) {
                    return $row->template->name ?? '';
                })->addColumn('action', function ($row) {
                    $html = '<a href="' . route('chatbot.update', $row->id) . '" class="btn btn-outline-warning btn-icon fs-16 ">
                                <i class="bx bx-pencil"></i>
                            </a>
                            <a href="javascript:void(0);" onclick="deleteData(`' . $row->id . '`)" class="btn btn-outline-danger btn-icon fs-16 deletebutton">
                                <i class="bx bx-trash "></i>
                            </a>';

                    return $html;
                })->rawColumns(['action'])
                ->make(true);
        }

        return view('chatbot.index', ['page'  => __('page.chatbot.page'), 'breadcumb' => true], compact('params'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Page
    |--------------------------------------------------------------------------
    */

    public function create(Request $request)
    {
        $templates      = $this->templateObserver->getData($request)->where("for_waba", 'no')->where('meta_account_id', null)->get(['id', 'name']);
        $devices        = $this->deviceObserver->getData($request)->get(['id', 'phone']);
        $livechats      = $this->livechatObserver->getData($request)->get(['id', 'name']);
        $telegrams      = $this->telegramService->getData($request)->get(['id', 'name']);
        $instagrams     = $this->instagramService->getData($request)->get(['id', 'name']);
        $messengers     = $this->messengerService->getData($request)->get(['id', 'page_name']);
        $wabaDevices    = \App\Models\WhatsappKeyAccount::where('status', 'active')->get(['id', 'phone', 'meta_data']);
        return view('chatbot.create', ['page' => __('page.chatbot.add'), 'breadcumb' => true], compact('templates', 'devices', 'livechats', 'telegrams', 'instagrams', 'messengers', 'wabaDevices'));
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Update Page
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, ChatBot $bot)
    {
        $templates      = $this->templateObserver->getData($request)->where('for_waba', 'no')->where('meta_account_id', null)->get(['id', 'name']);
        $devices        = $this->deviceObserver->getData($request)->get(['id', 'phone']);
        $livechats      = $this->livechatObserver->getData($request)->get(['id', 'name']);
        $telegrams      = $this->telegramService->getData($request)->get(['id', 'name']);
        $instagrams     = $this->instagramService->getData($request)->get(['id', 'name']);
        $messengers     = $this->messengerService->getData($request)->get(['id', 'page_name']);
        $wabaDevices    = \App\Models\WhatsappKeyAccount::where('status', 'active')->get(['id', 'phone', 'meta_data']);
        return view('chatbot.update', ['page' => __("page.chatbot.edit"), 'breadcumb' => true], compact('bot', 'templates', 'devices', 'livechats', 'telegrams', 'instagrams', 'messengers', 'wabaDevices'));
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Create Data
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {

        $this->validate($request, [
            'keyword'           => 'required',
            'device'            => 'array',
            'method'            => 'required|in:text,template,image',
            'template'          => 'required_if:method,template',
            'url'               => 'required_if:method,image',
            'message'           => 'required_if:method,text',
            'livechats'         => 'array',
            'instagrams'        => 'array',
            'messengers'        => 'array'
        ]);

        $validationCheck = $this->chatBotObserver->checkLimit();

        if ($validationCheck == false) {
            return redirect()->back()->with(['gagal'    => __('validation.chatbot_limit')]);
        }

        $chatbot = $this->chatBotObserver->createData($request);

        if ($chatbot->reply_method == 'image') {
            $this->chatBotObserver->createImages($request, $chatbot);
        }

        return redirect()->route('chatbot')->with(['flash'    => __('general.success_add_data')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Update Data
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, ChatBot $bot)
    {
        $this->validate($request, [
            'keyword'           => 'required',
            'device'            => 'array',
            'method'            => 'required|in:text,template,image',
            'template'          => 'required_if:method,template',
            'message'           => 'required_if:method,text',
            'livechats'         => 'array',
            'instagrams'        => 'array',
            'messengers'        => 'array'
        ]);

        $this->chatBotObserver->updateData($request, $bot);

        $bot->details()->delete();
        if ($bot->reply_method == 'image') {
            $this->chatBotObserver->createImages($request, $bot);
        }

        return redirect()->route('chatbot')->with(['flash'    => __('general.success_update')]);
    }


    /*
    |--------------------------------------------------------------------------
    | 6. Delete Data
    |--------------------------------------------------------------------------
    */

    public function delete(ChatBot $bot)
    {
        $bot->details()->delete();
        $this->chatBotObserver->deleteData($bot);

        return response()->json([
            'status'    => true,
            'message'   => __('general.success_deleted')
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 7. Delete Multiple Data
    |--------------------------------------------------------------------------
    */

    public function deleteMultiple(Request $request)
    {
        $ids = $request->ids;
        if (!empty($ids)) {
            ChatBotImage::whereHas('chatbot', function ($q) use ($ids) {
                return $q->whereIn('id', $ids);
            })->delete();

            ChatBot::whereIn('id', $ids)->delete();
            return response()->json(['success' => true, 'message' => __('general.success_deleted')]);
        }

        return response()->json(['success' => false, 'message' => __('general.choosed_not_found')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 8. Import ChatBot AutoReply
    |--------------------------------------------------------------------------
    */

    public function import(Request $request)
    {
        $this->validate($request, [
            'file'  => 'mimes:xlsx'
        ]);

        if ($request->file) {

            $import = Excel::toArray(new ChatBotImport(), $request->file('file'));

            if (count($import[0]) > 0) {


                try {

                    DB::beginTransaction();

                    foreach ($import[0] as $d) {
                        if ($d['keyword'] != null) {


                            $text           = null;
                            $deviceID       = WhatsappDevice::where('id', explode(",", $d['device']))->pluck("id")->toArray();

                            if ($d['method'] == 'template') {
                                $text       = MessageTemplate::find($d['template']);
                            }

                            if ($d['method'] == 'text') {
                                $text       = $d['text'];
                            }

                            if ($text != null && count($deviceID) > 0) {
                                ChatBot::create([
                                    'keyword'           => $d['keyword'],
                                    'select_device'     => implode(",", $deviceID),
                                    'reply_method'      => $d['method'],
                                    'template_id'       => $d['method'] == 'template' ? $text->id : null,
                                    'message'           => $d['method'] == 'text' ? $text : null
                                ]);
                            }
                        }
                    }

                    DB::commit();

                    return redirect()->back()->with(['flash'    => __('general.success_import')]);
                } catch (\Exception $e) {

                    DB::rollBack();

                    return redirect()->back()->with(['gagal'    => $e->getMessage()]);
                }
            } else {
                return redirect()->back()->with(['gagal'    => __('general.file_not_reader')]);
            }
        }
    }
}
