<?php

namespace App\Http\Controllers\Administrator\Saas;

use App\Http\Controllers\Controller;
use App\Models\Blash\BlashDetail;
use App\Models\Merchant\Merchant;
use App\Models\Package\PackageTransaction;
use App\Models\Setting;
use App\Models\User;
use App\Observers\Blash\LogObserver;
use App\Observers\Saas\BusinessObserver;
use App\Observers\Saas\MerchantCategoryObserver;
use App\Observers\Saas\MerchantObserver;
use App\Observers\UserObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MerchantController extends Controller
{
    protected $merchantObserver;
    protected $logsObserver;
    protected $businessObserver;
    protected $categoryObserver;
    protected $userObserver;

    public function __construct(MerchantObserver $merchantObserver, LogObserver $logObserver, BusinessObserver $businessObserver, MerchantCategoryObserver $categoryObserver, UserObserver $userObserver)
    {
        $this->merchantObserver     = $merchantObserver;
        $this->logsObserver         = $logObserver;
        $this->businessObserver     = $businessObserver;
        $this->categoryObserver     = $categoryObserver;
        $this->userObserver         = $userObserver;
    }

    public function index(Request $request)
    {
        $merchants      = $this->merchantObserver->getData($request)->get();
        return view('admin.merchants.index', ['page'     => __('page.customer.page'), 'breadcumb' => false], compact('merchants'));
    }

    public function create(Request $request)
    {
        $categories = $this->categoryObserver->getData($request)->get(['id', 'name']);
        return view('admin.merchants.create', ['page' => __('merchant.add_merchant')], compact('categories'));
    }

    public function store(Request $request)
    { 
        $this->validate($request, [
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email',
            'phone'             => 'required|numeric|unique:users,phone',
            'password'          => 'required|min:8',
            'business_name'     => 'required|string|max:255',
            'category'          => 'required'
        ]);

        DB::beginTransaction();

        try { 
            $formattedPhone = '62' . ltrim($request->phone, '0');

              
            $user = User::create([ 
                'name'              => $request->name,
                'email'             => $request->email,
                'phone'             => $formattedPhone,
                'password'          => Hash::make($request->password), 
                'gender'            => $request->gender ?? 'male'
            ]);
 
            $merchant = Merchant::create([ 
                'owner_id'          => $user->id,
                'name'              => $request->business_name,
                'description'       => $request->description ?? '',
                'category_id'       => $request->category,
            ]);
 
            $user->update([
                'merchant_id' => $merchant->id
            ]);

            DB::commit();

            return redirect()->route('merchants')->with(['flash' => __('merchant.merchant_user_added')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with(['gagal' => __('messages.failed_to_add_data', ['error' => $e->getMessage()])]);
        }
    }


    public function getByJquery(Request $request)
    {
        $merchants      = $this->merchantObserver->getData($request)->limit(20)->get(['id', 'name']);
        return response()->json($merchants);
    }

    public function detail(Request $request, Merchant $merchant)
    {

        $data = [
            'business'      => Setting::withoutGlobalScopes()->where("merchant_id", $merchant->id)->count(),
            'package'       => PackageTransaction::where('status', 'success')->where('type', 'package')->where("merchant_id", $merchant->id)->sum('final_total'),
            'topup'         => PackageTransaction::where('status', 'success')->where('type', 'topup')->where("merchant_id", $merchant->id)->sum('final_total'),
            'users'         => User::withoutGlobalScopes()->where("merchant_id", $merchant->id)->count()
        ];

        $businesses      = $this->businessObserver->getForAdmin($request)->where('merchant_id', $merchant->id)->get();
        return view('admin.merchants.detail', ['page'    => __('page.customer.detail'), 'breadcumb' => true], compact('merchant', 'data', 'businesses'));
    }

    public function merchantAnalisis(Merchant $merchant)
    {
        $senderData     = array();
        $notSenderData  = array();
        $dateData       = array();

        $blashData = BlashDetail::whereHas('parent', function ($q) use ($merchant) {
            return $q->where("merchant_id", $merchant->id);
        })->selectRaw('LEFT(created_at, 10) as date, 
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

    public function changeStatus(Merchant $merchant)
    {

        $merchant->update([
            'status'        => $merchant->status == 'active' ? 'no' : 'active'
        ]);

        return response()->json([
            'message'   => __('general.success_update'),
            'merchant'  => $merchant->status
        ]);
    }

    public function signIntUser(Merchant $merchant)
    {
        if ($merchant->owner) {
            Auth::login($merchant->owner);
            return redirect()->route('index')->with(['flash' => __('merchant.you_are_logged_in_as') . $merchant->owner->name]);
        }

        return redirect()->back()->with(['gagal'    => __('customer.user_not_found')]);
    }

    public function update(Request $request, Merchant $merchant)
    {
        $categories     = $this->categoryObserver->getData($request)->get(['id', 'name']);
        return view('admin.merchants.update', ['page'    => __('merchant.business_edit')], compact('merchant', 'categories'));
    }

    public function edit(Request $request, Merchant $merchant)
    {

        $merchant->update([
            'name'                      => $request->name,
            'merchant_category_id'      => $request->categpry,
            'address'                   => $request->address,
            'zip_code'                  => $request->zip_code,
        ]);

        return redirect()->route('merchants')->with(['flash'    => __('merchant.merchant_updated')]);
    }

    public function owner(Merchant $merchant)
    {
        $user   = $merchant->owner;
        return view('admin.merchants.owner', ['page'    => __('merchant.business_owner_edit')], compact('merchant', 'user'));
    }

    public function updateOwner(Request $request, Merchant $merchant)
    {

        $this->validate($request, [
            'name'      => 'required',
            'email'     => 'required|unique:users,email,' . $merchant->owner_id,
            'phone'     => 'required|unique:users,phone,' . $merchant->owner_id,
            'image'     => 'mimes:jpg,jpeg,png',
            'gender'    => 'required|in:male,female'
        ]);

        $image  = '';

        if ($request->image) {
            $this->unlinkFile($merchant->owner->photo);
            $image =  $this->uploadImage($request, 'image', 'users');
        }

        $this->userObserver->updateData($request, $merchant->owner, $image);

        return redirect()->route('merchants')->with(['flash'    => __('merchant.business_owner_updated')]);
    }

    public function password(Request $request, Merchant $merchant)
    {
        $this->validate($request, [
            'password'      => 'required|min:8',
            'confirm'       => 'required',
        ]);

        if ($request->password != $request->confirm) {
            return back()->with(['gagal' => __('validation.password_must_same')]);
        }


        $this->userObserver->changePassword($request, $merchant->owner);
        return redirect()->route('merchants')->with(['flash'    => __('merchant.business_owner_updated')]);
    }

    public function delete(Merchant $merchant)
    {

        try {

            DB::beginTransaction();

            $merchant->delete();

            DB::commit();

            return redirect()->back()->with(['flash'   => __('merchant.merchant_deleted')]);
        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()->back()->with(['gagal'    => $e->getMessage()]);
        }
    }
}
