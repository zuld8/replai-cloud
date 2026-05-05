<?php

namespace App\Http\Controllers\Administrator\Saas;

use App\Http\Controllers\Controller;
use App\Models\Package\Package;
use App\Observers\Saas\PricingObserver;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    protected $pricingObserver;

    public function __construct(PricingObserver $pricingObserver)
    {
        $this->pricingObserver      = $pricingObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. List Category Page
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $packages     = $this->pricingObserver->getData($request)->get();
        return view('admin.package.index', ['page' => __('page.package.page'), 'breadcumb' => true], compact('packages'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Package Page
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin.package.create', ['page' => __('page.package.add'), 'breadcumb' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Update Package Page
    |--------------------------------------------------------------------------
    */

    public function update(Package $package)
    {
        return view('admin.package.update', ['page' => __('page.package.edit'), 'breadcumb' => true], compact('package'));
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Store Package Data
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'                          => 'required|string',
            'price'                         => 'required_if:trial_version,no',
            'cek_ongkir'                    => 'required|in:yes,no',
            'google_sheet'                  => 'required|in:yes,no',
            'trial_version'                 => 'required|in:yes,no',
            'limit_user_option'             => 'required|in:yes,no',
            'users_limit'                   => 'required_if:limit_user_option,yes',
            'limit_whatsapp_option'         => 'required|in:yes,no',
            'limit_whatsapp_priode'         => 'required_if:limit_whatsapp_option,yes|in:monthly,yearly,daily',
            'whatsapp_limit'                => 'required_if:limit_whatsapp_option,yes',
            'limit_email_option'            => 'required|in:yes,no',
            'limit_email_priode'            => 'required_if:limit_email_option,yes|in:monthly,yearly,daily',
            'email_limit'                   => 'required_if:limit_email_option,yes',
            'limit_scrapp_option'           => 'required|in:yes,no',
            'limit_scrapp_priode'           => 'required_if:limit_scrapp_option,yes|in:monthly,yearly,daily',
            'scrapp_limit'                  => 'required_if:limit_scrapp_option,yes',
            'limit_device'                  => 'required|in:yes,no',
            'device_limit'                  => 'required_if:limit_device,yes',
            'limit_template'                => 'required|in:yes,no',
            'template_limit'                => 'required_if:limit_template,yes',
            'limit_ai_training'             => 'required|in:yes,no',
            'ai_training_limit'             => 'required_if:limit_ai_training,yes',
            'limit_chatbot'                 => 'required|in:yes,no',
            'chatbot_limit'                 => 'required_if:limit_chatbot,yes',
            'ai_response'                   => 'required|numeric',
            'livechat_limit'                => 'required|in:yes,no',
            'limit_livechat'                => 'required_if:livechat_limit,yes',
            'days_option'                   => 'required|in:limited,unlimited',
            'add_days'                      => 'required_if:days_option,limited|numeric',
            'limit_instagram'               => 'required|in:yes,no',
            'instagram'                     => 'required_if:limit_instagram,yes',
            'limit_messanger'               => 'required|in:yes,no',
            'messanger'                     => 'required_if:limit_messanger,yes',
            'limit_waba'                    => 'required|in:yes,no',
            'waba_limit'                    => 'required_if:limit_waba,yes',
            'limit_telegram'                => 'required|in:yes,no',
            'telegram'                      => 'required_if:limit_telegram,yes',
            'storage'                       => 'required|numeric|min:0',
            'mua_limit'                     => 'required|numeric|min:0',
            'mua_limit_optin'               => 'required|in:yes,no',
            'max_per_upload'                => 'required|numeric|min:1',
            'max_total_rag'                 => 'required|numeric|min:1',
        ]);

        $package = $this->pricingObserver->createData($request);

        return redirect()->route('packages')->with(['flash'    => __('general.success_add_data')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Update Package Data
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, Package $package)
    {
        $this->validate($request, [
            'name'                          => 'required|string',
            'price'                         => 'required_if:trial_version,no',
            'cek_ongkir'                    => 'required|in:yes,no',
            'google_sheet'                  => 'required|in:yes,no',
            'trial_version'                 => 'required|in:yes,no',
            'limit_user_option'             => 'required|in:yes,no',
            'users_limit'                   => 'required_if:limit_user_option,yes',
            'limit_whatsapp_option'         => 'required|in:yes,no',
            'limit_whatsapp_priode'         => 'required_if:limit_whatsapp_option,yes|in:monthly,yearly,daily',
            'whatsapp_limit'                => 'required_if:limit_whatsapp_option,yes',
            'limit_email_option'            => 'required|in:yes,no',
            'limit_email_priode'            => 'required_if:limit_email_option,yes|in:monthly,yearly,daily',
            'email_limit'                   => 'required_if:limit_email_option,yes',
            'limit_scrapp_option'           => 'required|in:yes,no',
            'limit_scrapp_priode'           => 'required_if:limit_scrapp_option,yes|in:monthly,yearly,daily',
            'scrapp_limit'                  => 'required_if:limit_scrapp_option,yes',
            'limit_device'                  => 'required|in:yes,no',
            'device_limit'                  => 'required_if:limit_device,yes',
            'limit_template'                => 'required|in:yes,no',
            'template_limit'                => 'required_if:limit_template,yes',
            'limit_ai_training'             => 'required|in:yes,no',
            'ai_training_limit'             => 'required_if:limit_ai_training,yes',
            'limit_chatbot'                 => 'required|in:yes,no',
            'chatbot_limit'                 => 'required_if:limit_chatbot,yes',
            'ai_response'                   => 'required|numeric',
            'livechat_limit'                => 'required|in:yes,no',
            'limit_livechat'                => 'required_if:livechat_limit,yes',
            'days_option'                   => 'required|in:limited,unlimited',
            'add_days'                      => 'required_if:days_option,limited|numeric',
            'limit_instagram'               => 'required|in:yes,no',
            'instagram'                     => 'required_if:limit_instagram,yes',
            'limit_messanger'               => 'required|in:yes,no',
            'messanger'                     => 'required_if:limit_messanger,yes',
            'limit_waba'                    => 'required|in:yes,no',
            'waba_limit'                    => 'required_if:limit_waba,yes',
            'limit_telegram'                => 'required|in:yes,no',
            'telegram'                      => 'required_if:limit_telegram,yes',
            'storage'                       => 'required|numeric|min:0',
            'mua_limit'                     => 'required|numeric|min:0',
            'mua_limit_optin'               => 'required|in:yes,no',
            'max_per_upload'                => 'required|numeric|min:1',
            'max_total_rag'                 => 'required|numeric|min:1',
        ]);

        $this->pricingObserver->updateData($request, $package);

        return redirect()->route('packages')->with(['flash'    => __('general.success_update')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 6. Delete Package Data
    |--------------------------------------------------------------------------
    */

    public function delete(Package $package)
    {
        $this->pricingObserver->deleteData($package);

        return redirect()->back()->with(['flash'    => __('general.success_deleted')]);
    }
}
