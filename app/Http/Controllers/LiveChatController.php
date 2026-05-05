<?php

namespace App\Http\Controllers;

use App\Models\LiveChat; 
use App\Observers\ChatBot\FineTunnelObserver;
use App\Observers\LiveChatObserver;
use App\Observers\UserObserver;
use Illuminate\Http\Request;

class LiveChatController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Whatsapp Device Controllers
    |--------------------------------------------------------------------------
    */

    protected $livechatObserver;
    protected $usersObserver;
    protected $fineTunnelObserver;
    protected $livechatsObserver;

    public function __construct(LiveChatObserver $livechatObserver, UserObserver $userObserver, FineTunnelObserver $fineTunnelObserver, LiveChatObserver $liveChatObserver)
    {
        $this->livechatObserver         = $livechatObserver;
        $this->usersObserver            = $userObserver;
        $this->fineTunnelObserver       = $fineTunnelObserver;
        $this->livechatObserver         = $liveChatObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Whatsapp Device List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {

        $livechats     = $this->livechatObserver->getData($request)->paginate(12);
        $pagination       = array(
            'current_page'      => $livechats->currentPage(),
            'to_page'           => $livechats->lastPage(),
            'per_page'          => $livechats->perPage(),
            'first_item'        => $livechats->firstItem(),
            'last_item'         => $livechats->lastItem(),
            'links'             => $livechats->linkCollection()->toArray()
        );
        return view('livechat.index', ['page'    => __('livechat.livechat_list'), 'breadcumb' => true], compact('livechats', 'pagination'));
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

        return view('livechat.create', ['page'   => __('livechat.add_livechat'), 'breadcumb' => true], compact('fineTunnels', 'users'));
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Update Page
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, LiveChat $chat)
    {
        $users          = $this->usersObserver->getData($request)->get(['id', 'name']);
        $fineTunnels    = $this->fineTunnelObserver->getData($request)->get(['name', 'id']);
        $faqs           = $chat->faqs != null ? json_decode($chat->faqs, true) : array();
        $sosmed         = $chat->sosmed != null ? json_decode($chat->sosmed, true) : array(); 
        return view('livechat.update', ['page'   => __('livechat.edit_livechat'), 'breadcumb' => true], compact('fineTunnels', 'chat', 'users', 'faqs', 'sosmed'));
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Delete Whatsapp Device
    |--------------------------------------------------------------------------
    */

    public function delete(LiveChat $chat)
    {

        $this->livechatObserver->deleteData($chat);

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
            'type'                  => 'required|in:ai,chatbot,all',
            'tunnel'                => 'required_if:type,ai,all',
            'agent'                 => 'required|array',
            'image'                 => 'mimes:jpg,jpeg,png,webp'
        ]);

        $validationCheck = $this->livechatObserver->checkLimit();

        if ($validationCheck == false) {
            return redirect()->back()->with(['gagal'    => __('livechat.livechat_limit_reached')])->withInput();
        }

        $image  = '';

        if ($request->image) {
            $image =  $this->uploadImage($request, 'image', 'users');
        }

        $this->livechatObserver->createData($request, $image);
        return redirect()->route('livechats')->with(['flash' => __('general.success_add_data')]);
    }


    /*
    |--------------------------------------------------------------------------
    | 7. Update Data to Database
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, LiveChat $chat)
    {
        $this->validate($request, [
            'name'                  => 'required',
            'type'                  => 'required|in:ai,chatbot,all',
            'tunnel'                => 'required_if:type,ai,all',
            'agent'                 => 'required|array',
            'image'                 => 'mimes:jpg,jpeg,png,webp'
        ]);

        $image  = '';

        if ($request->image) {
            $this->unlinkFile($chat->photo);
            $image =  $this->uploadImage($request, 'image', 'users');
        }

        $this->livechatObserver->updateData($request, $chat, $image);
        return redirect()->route('livechats')->with(['flash' => __('general.success_update')]);
    }
}
