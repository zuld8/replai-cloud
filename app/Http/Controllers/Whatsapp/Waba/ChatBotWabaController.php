<?php

namespace App\Http\Controllers\Whatsapp\Waba;

use App\Http\Controllers\Controller;
use App\Http\Requests\Whatsapp\Official\ChatbotRequest;
use App\Http\Resources\Waba\Chatbot\ChatbotDetailResource;
use App\Models\ChatBot\ChatBot;
use App\Models\MetaAccount; 
use App\Observers\ChatBot\ChatBotObserver;
use App\Observers\Master\TemplateObserver;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ChatBotWabaController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ChatBot Auto Reply For Waba Controllers
    |--------------------------------------------------------------------------
    */

    protected $chatBotObserver;
    protected $templateObserver;

    public function __construct(
        ChatBotObserver $chatBotObserver,
        TemplateObserver $templateObserver
    ) {
        $this->chatBotObserver      = $chatBotObserver;
        $this->templateObserver     = $templateObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. ChatBot Auto Reply List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request, MetaAccount $meta)
    {
        $queryArray     = $request->all();
        $params         = http_build_query($queryArray);

        if ($request->ajax()) {

            $stores         = $this->chatBotObserver->getData($request)->where('meta_account_id', $meta->id);

            return DataTables::of($stores)
                ->addColumn('method', function ($row) {
                    return $row->reply_method == 'text' ? 'Text' : ($row->reply_method == 'image' ? 'Image' : 'Template');
                })->addColumn('template', function ($row) {
                    return $row->template->name ?? '';
                })->addColumn('action', function ($row) {
                    $html = '<a href="' . route('waba.chatbot.update', [$row->meta_account_id, $row->id]) . '" class="btn btn-outline-warning btn-icon fs-16 ">
                                <i class="bx bx-pencil"></i>
                            </a>
                            <a href="javascript:void(0);" onclick="deleteData(`' . $row->id . '`)" class="btn btn-outline-danger btn-icon fs-16 deletebutton">
                                <i class="bx bx-trash "></i>
                            </a>';

                    return $html;
                })->rawColumns(['action'])
                ->make(true);
        }

        return view('waba.chatbot.index', ['page'  => __('page.chatbot.page'), 'breadcumb' => true], compact('params', 'meta'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Page
    |--------------------------------------------------------------------------
    */

    public function create(Request $request, MetaAccount $meta)
    {
        return view('waba.chatbot.create', ['page' => __('page.chatbot.add'), 'breadcumb' => true], compact('meta'));
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Update Page
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, MetaAccount $meta, ChatBot $bot)
    {
        return view('waba.chatbot.create', ['page' => __("page.chatbot.edit"), 'breadcumb' => true], compact('meta', 'bot'));
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Create Data
    |--------------------------------------------------------------------------
    */

    public function store(ChatbotRequest $request, MetaAccount $meta)
    {
 
        $validationCheck = $this->chatBotObserver->checkLimit();

        if ($validationCheck == false) {
            return response([
                'status'    => false,
                'message'   =>  __('validation.template_limit')
            ], 422);
        }

        $files  = '';
        if ($request->has('files')) {
            $files  = $this->uploadImage($request, 'files', 'template');
        }

        $this->chatBotObserver->createDataForOfficial($request, $meta, $files);

        return response()->json([
            'status'    => true,
            'message'   =>  __('general.success_add_data')
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Update Data
    |--------------------------------------------------------------------------
    */

    public function edit(ChatbotRequest $request, MetaAccount $meta, ChatBot $bot)
    {

        $files  = '';
        if ($request->has('files')) {
            $files  = $this->uploadImage($request, 'files', 'template');
        }

        $this->chatBotObserver->updateDataForOfficial($request, $meta, $bot, $files);

        return response()->json([
            'status'    => true,
            'message'   =>  __('general.success_update')
        ], 200);
    }


    /*
    |--------------------------------------------------------------------------
    | 6. Detail Page
    |--------------------------------------------------------------------------
    */

    public function details(MetaAccount $meta, ChatBot $bot)
    {
        return response()->json([
            'status'    => true,
            'detail'    => ChatbotDetailResource::make($bot)
        ], 200);
    }
}
