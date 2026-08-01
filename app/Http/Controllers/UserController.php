<?php

namespace App\Http\Controllers;

use App\Http\Resources\Master\TeamListResource;
use App\Models\User;
use App\Observers\Saas\BusinessObserver;
use App\Observers\UserObserver;
use App\Process\MasterData\UploadImageProcess;
use App\Services\Master\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;
use App\Models\WhatsappDevice;
use App\Models\WhatsappKeyAccount;
use App\Models\TelegramKey;
use App\Models\Meta\InstagramAccount;
use App\Models\Meta\MessengerAccount;
use App\Models\LiveChat;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Users Controllers
    |--------------------------------------------------------------------------
    */

    protected $usersObserver;
    protected $uploadImageProcess;
    protected $businessObserver;
    protected $roleService;

    public function __construct(UserObserver $userObserver, UploadImageProcess $uploadImageProcess, BusinessObserver $businessObserver, RoleService $roleService)
    {
        $this->usersObserver        = $userObserver;
        $this->uploadImageProcess   = $uploadImageProcess;
        $this->businessObserver     = $businessObserver;
        $this->roleService          = $roleService;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Users List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $users       = $this->usersObserver->getData($request)->get();
        $businesses  = $this->businessObserver->getData($request)->get(['id', 'name', 'created_at']);
        $roles       = $this->roleService->getData($request);
        try {
            $bizId     = my_business();
            $platforms = $this->getPlatformChannels($bizId);
        } catch (\Throwable $e) {
            \Log::warning('getPlatformChannels failed: ' . $e->getMessage());
            $platforms = ['devices'=>collect(),'wabas'=>collect(),'telegrams'=>collect(),'instagrams'=>collect(),'messengers'=>collect(),'livechats'=>collect()];
        }
        // Build platform map for card display (1 query per table, no N+1)
        $platformMap = $this->buildPlatformMap($users->pluck('id')->toArray());
        // Kuota agen
        try {
            $bizId2    = my_business();
            $biz2      = \App\Models\Setting::where('id', $bizId2)->first(['id']);
            $pkg2      = $biz2?->package_active;
            $userLimit = ($pkg2 && $pkg2->limit_user_option === 'yes') ? (int)$pkg2->users_limit : null;
        } catch (\Throwable $e) {
            $userLimit = null;
        }
        $userQuota = ['count' => count($users), 'limit' => $userLimit];
        return view('users.index', ['page' => __('page.user.page'), 'breadcumb' => false], compact('users', 'businesses', 'roles', 'platforms', 'platformMap', 'userQuota'));
    }


    public function components(Request $request)
    {
        $users   = $this->usersObserver->getData($request)->get(['id', 'name']);
        return response()->json([
            'users'     => TeamListResource::collection($users),
        ], 200);
    }

    public function getJson(Request $request, User $user)
    {
        $userPlatforms = $this->getUserPlatformIds($user->id);
        $bizIds        = $user->business_id ? array_map('trim', explode(',', $user->business_id)) : [];
        $activeRole  = $user->roles->first();
        $isPrimary   = $user->merchant && optional($user->merchant->owner)->id === $user->id;

        return response()->json([
            'id'            => $user->id,
            'name'          => $user->name,
            'email'         => $user->email,
            'phone'         => $user->phone,
            'gender'        => $user->gender,
            'role_id'       => $activeRole?->id ?? '',
            'role_name'     => $activeRole?->name ?? 'Administrator',
            'is_primary'    => $isPrimary,
            'photo'         => $user->photo ? asset('storage/' . $user->photo) : null,
            'businesses'    => $bizIds,
            'userPlatforms' => $userPlatforms,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Page
    |--------------------------------------------------------------------------
    */

    public function create(Request $request)
    {
        $businesses     = $this->businessObserver->getData($request)->get(['id', 'name', 'created_at']);
        $roles          = $this->roleService->getData($request);
        return view('users.create', ['page'   => __('page.user.add'), 'breadcumb' => true], compact('businesses', 'roles'));
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Update Page
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, User $user)
    {
        $businesses      = $this->businessObserver->getData($request)->get(['id', 'name', 'created_at']);
        $roles           = $this->roleService->getData($request);
        try {
            $bizId         = my_business();
            $platforms     = $this->getPlatformChannels($bizId);
            $userPlatforms = $this->getUserPlatformIds($user->id);
        } catch (\Throwable $e) {
            \Log::warning('update getPlatformChannels failed: ' . $e->getMessage());
            $empty         = ['devices'=>collect(),'wabas'=>collect(),'telegrams'=>collect(),'instagrams'=>collect(),'messengers'=>collect(),'livechats'=>collect()];
            $platforms     = $empty;
            $userPlatforms = ['devices'=>[],'wabas'=>[],'telegrams'=>[],'instagrams'=>[],'messengers'=>[],'livechats'=>[]];
        }
        return view('users.update', ['page' => __('page.user.edit'), 'breadcumb' => true], compact('user', 'businesses', 'roles', 'platforms', 'userPlatforms'));
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Change Password
    |--------------------------------------------------------------------------
    */

    public function changePassword(Request $request, User $user)
    {
        // A2: Owner hanya bisa ganti password sendiri
        $isOwner = $user->merchant && optional($user->merchant->owner)->id === $user->id;
        abort_if($isOwner && auth()->id() !== $user->id, 403, 'Password owner hanya bisa diubah oleh owner sendiri.');

        $this->validate($request, [
            'password'      => 'required|min:8',
            'confirm'       => 'required'
        ]);

        if ($request->password != $request->confirm) {
            return redirect()->back()->with(['gagal'    => __('validation.password_must_same')]);
        }

        $this->usersObserver->changePassword($request, $user);
        return redirect()->route('users')->with(['flash' => __('general.success_update')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Delete User
    |--------------------------------------------------------------------------
    */

    public function delete(User $user)
    {
        $isAjax = request()->ajax();

        if ($user->merchant && (string)$user->merchant->owner_id === (string)$user->id) { // A6: cek owner_id langsung
            $msg = 'Pengguna ini merupakan owner bisnis dan tidak dapat di hapus';
            return $isAjax
                ? response()->json(['success' => false, 'message' => $msg])
                : redirect()->back()->with(['gagal' => $msg]);
        }

        try {
            DB::beginTransaction();

            // Delete related agent records first (FK constraints)
            DB::table('device_agents')->where('user_id', $user->id)->delete();
            DB::table('waba_agents')->where('user_id', $user->id)->delete();
            DB::table('telegram_agents')->where('user_id', $user->id)->delete();
            DB::table('instagram_agents')->where('user_id', $user->id)->delete();
            DB::table('messenger_agents')->where('user_id', $user->id)->delete();
            DB::table('live_chat_agents')->where('user_id', $user->id)->delete();
            DB::table('ticket_agents')->where('agent_id', $user->id)->delete();
            DB::table('ticket_notes')->where('user_id', $user->id)->delete();

            $this->unlinkFile($user->photo);
            $this->usersObserver->deleteData($user);

            DB::commit();
            $msg = __('general.success_deleted');
            return $isAjax
                ? response()->json(['success' => true, 'message' => $msg])
                : redirect()->back()->with(['flash' => $msg]);
        } catch (\Exception $e) {
            DB::rollBack();
            $msg = 'Gagal menghapus pengguna: ' . $e->getMessage();
            return $isAjax
                ? response()->json(['success' => false, 'message' => $msg])
                : redirect()->back()->with(['gagal' => $msg]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 6. Store Data to Database
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'          => 'required',
            'email'         => 'required|email|unique:users,email,NULL,id',
            'phone'         => 'required|numeric|unique:users,phone,NULL,id',
            'password'      => 'required|min:8',
            'photo'         => 'mimes:jpg,jpeg,png',
            'gender'        => 'required|in:male,female',
            'role'          => 'required|uuid'
        ]);

        $validationCheck = $this->usersObserver->checkLimit();

        if ($validationCheck == false) {
            return redirect()->back()->with(['gagal'    => __('user.limit')]);
        }


        try {
            DB::beginTransaction();

            $image  = '';

            if ($request->image) {
                $image =  $this->uploadImage($request, 'image', 'users');
            }

            if ($image == '') {
                $image = $this->uploadImageProcess->createDafaultMedia($request->name, 'uploads/users/');
            }

            $user = $this->usersObserver->createData($request, $image);
            $this->syncPlatformAgents($user->id, $request);
            $user->assignRole($user->role_access); 

            DB::commit();
            return redirect()->route('users', $user->id)->with(['flash' => __('general.success_add_data')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with([
                'gagal' => $e->getMessage()
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 7. Update Data to Database
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, User $user)
    {
        $this->validate($request, [
            'name'          => 'required',
            'email'         => "required|email|unique:users,email,{$user->id}",
            'phone'         => "required|numeric|unique:users,phone,{$user->id}",
            'photo'         => 'mimes:jpg,jpeg,png',
            'gender'        => 'required|in:male,female',
            'role'          => 'required|uuid'
        ]);

        try {
            DB::beginTransaction();

            $image  = '';

            if ($request->hasFile('image')) {
                $this->unlinkFile($user->photo);
                $image = $this->uploadImage($request, 'image', 'users');
            } elseif ($request->hasFile('photo')) {
                $this->unlinkFile($user->photo);
                $image = $this->uploadImage($request, 'photo', 'users');
            }

            // 🔒 Guard: primary/owner user — role cannot be changed
            if ($user->merchant && optional($user->merchant->owner)->id === $user->id) {
                $request->merge(['role' => $user->role_id]);
            }

            $this->usersObserver->updateData($request, $user, $image);
            $this->syncPlatformAgents($user->id, $request);
            $user->fresh();

            $user->syncRoles([$user->role_access]); 

            DB::commit();
            return redirect()->route('users')->with(['flash' => __('general.success_update')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with([
                'gagal' => $e->getMessage()
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Platform Agent Helpers
    |--------------------------------------------------------------------------
    */

    private function buildPlatformMap(array $userIds): array
    {
        if (empty($userIds)) return [];
        $map  = [];
        // B: 6 query total (bukan N+1 per user) — ambil identitas + status per channel
        $defs = [
            'waba'      => ['pivot'=>'waba_agents',      'src'=>'whatsapp_key_accounts','fk'=>'waba_id',      'label'=>'WhatsApp Business', 'ident'=>'phone',      'status_col'=>'status'],
            'device'    => ['pivot'=>'device_agents',    'src'=>'whatsapp_devices',     'fk'=>'device_id',    'label'=>'WhatsApp Personal', 'ident'=>'name',       'status_col'=>'status'],
            'telegram'  => ['pivot'=>'telegram_agents',  'src'=>'telegram_keys',        'fk'=>'telegram_id',  'label'=>'Telegram',          'ident'=>'name',       'status_col'=>'status'],
            'instagram' => ['pivot'=>'instagram_agents', 'src'=>'instagram_accounts',   'fk'=>'instagram_id', 'label'=>'Instagram',         'ident'=>'username',   'status_col'=>'status'],
            'messenger' => ['pivot'=>'messenger_agents', 'src'=>'messenger_accounts',   'fk'=>'messenger_id', 'label'=>'Messenger',         'ident'=>'page_name',  'status_col'=>'status'],
            'livechat'  => ['pivot'=>'live_chat_agents', 'src'=>'live_chats',           'fk'=>'livechat_id',  'label'=>'Live Chat',         'ident'=>'name',       'status_col'=>'type'],
        ];
        foreach ($defs as $type => $d) {
            $rows = DB::table($d['pivot'].' as p')
                ->join($d['src'].' as s', 's.id', '=', 'p.'.$d['fk'])
                ->whereIn('p.user_id', $userIds)
                ->select('p.user_id', DB::raw("s.{$d['ident']} as ident"), DB::raw("s.{$d['status_col']} as status"))
                ->get();
            foreach ($rows as $r) {
                $map[$r->user_id][] = ['type'=>$type, 'label'=>$d['label'], 'ident'=>$r->ident ?? '', 'status'=>$r->status ?? ''];
            }
        }
        return $map;
    }

    private function getPlatformChannels($bizId)
    {
        // Try session first, fallback to user's business_id column, then merchant's first setting
        if (!$bizId) {
            $user = check_user() ? my_user() : null;
            if ($user) {
                if ($user->business_id) {
                    $parts = explode(',', $user->business_id);
                    $bizId = trim($parts[0]);
                }
                if (!$bizId && $user->merchant_id) {
                    // Get first setting for this merchant from DB
                    $setting = DB::table('settings')->where('merchant_id', $user->merchant_id)->first(['id']);
                    $bizId   = $setting ? $setting->id : null;
                }
            }
        }
        $merchantId = (check_user() && my_user()) ? (my_user()->merchant_id ?? null) : null;
        // Use DB::table with correct column names per table (avoids model GlobalScope issues)
        return [
            'devices'    => $bizId ? DB::table('whatsapp_devices')
                                ->where('business_id', $bizId)
                                ->select('id', 'name', 'status')->get() : collect(),
            'wabas'      => $bizId ? DB::table('whatsapp_key_accounts')
                                ->where('business_id', $bizId)
                                ->select('id', 'phone', 'status')->get() : collect(),
            'telegrams'  => $bizId ? DB::table('telegram_keys')
                                ->where('business_id', $bizId)
                                ->select('id', 'name', 'status')->get() : collect(),
            'instagrams' => $bizId ? DB::table('instagram_accounts')
                                ->where('business_id', $bizId)
                                ->select('id', 'name', 'username', 'status')->get() : collect(),
            'messengers' => $bizId ? DB::table('messenger_accounts')
                                ->where('business_id', $bizId)
                                ->select('id', DB::raw('page_name as name'), 'page_username', 'status')->get() : collect(),
            'livechats'  => $bizId ? DB::table('live_chats')
                                ->where(function($q) use ($bizId, $merchantId) {
                                    $q->where('business_id', $bizId);
                                    if ($merchantId) $q->orWhere('merchant_id', $merchantId);
                                })
                                ->select('id', 'name', 'type')->get() : collect(),
        ];
    }

    private function getUserPlatformIds($userId)
    {
        $tables = [
            'devices'    => ['table' => 'device_agents',    'fk' => 'device_id'],
            'wabas'      => ['table' => 'waba_agents',      'fk' => 'waba_id'],
            'telegrams'  => ['table' => 'telegram_agents',  'fk' => 'telegram_id'],
            'instagrams' => ['table' => 'instagram_agents', 'fk' => 'instagram_id'],
            'messengers' => ['table' => 'messenger_agents', 'fk' => 'messenger_id'],
            'livechats'  => ['table' => 'live_chat_agents', 'fk' => 'livechat_id'],
        ];
        $result = [];
        foreach ($tables as $key => $cfg) {
            $result[$key] = DB::table($cfg['table'])
                ->where('user_id', $userId)
                ->pluck($cfg['fk'])
                ->toArray();
        }
        return $result;
    }

    private function syncPlatformAgents($userId, $request)
    {
        $bizId = my_business();
        // A3: mapping field → pivot + FK + source table (untuk validasi ownership bisnis)
        $map = [
            'devices'    => ['table'=>'device_agents',    'fk'=>'device_id',    'src'=>'whatsapp_devices'],
            'wabas'      => ['table'=>'waba_agents',      'fk'=>'waba_id',      'src'=>'whatsapp_key_accounts'],
            'telegrams'  => ['table'=>'telegram_agents',  'fk'=>'telegram_id',  'src'=>'telegram_keys'],
            'instagrams' => ['table'=>'instagram_agents', 'fk'=>'instagram_id', 'src'=>'instagram_accounts'],
            'messengers' => ['table'=>'messenger_agents', 'fk'=>'messenger_id', 'src'=>'messenger_accounts'],
            'livechats'  => ['table'=>'live_chat_agents', 'fk'=>'livechat_id',  'src'=>'live_chats'],
        ];
        foreach ($map as $field => $cfg) {
            DB::table($cfg['table'])->where('user_id', $userId)->delete();
            $requested = array_filter((array)$request->get($field, []));
            if (empty($requested)) continue;
            // A3: hanya insert ID yang BENAR milik bisnis ini (anti-IDOR)
            $valid = DB::table($cfg['src'])
                ->where('business_id', $bizId)
                ->whereIn('id', $requested)
                ->pluck('id')->all();
            foreach ($valid as $platformId) {
                DB::table($cfg['table'])->insert([
                    'id'         => Str::uuid()->toString(),
                    $cfg['fk']   => $platformId,
                    'user_id'    => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}