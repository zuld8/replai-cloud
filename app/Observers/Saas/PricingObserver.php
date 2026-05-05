<?php

namespace App\Observers\Saas;

use App\Models\Package\Package;
use App\Models\Package\PackageDetail;
use Illuminate\Http\Request;

class PricingObserver
{
    public function getData(Request $request, String $type = 'package')
    {
        return Package::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->where('type', $type)->orderBy('price', 'asc');
    }

    public function createData(Request $request)
    {
        return Package::create([
            'name'                          => $request->name,
            'price'                         => $request->trial_version == 'yes' ? 0 : $request->price,
            'add_days'                      => $request->days_option == 'limited' ? $request->add_days : 0,
            'days_option'                   => $request->days_option,
            'trial_version'                 => $request->trial_version,
            'limit_user_option'             => $request->limit_user_option,
            'users_limit'                   => $request->limit_user_option == 'yes' ? $request->users_limit : 0,
            'limit_whatsapp_option'         => $request->limit_whatsapp_option,
            'limit_whatsapp_priode'         => $request->limit_whatsapp_priode ?? 'daily',
            'whatsapp_limit'                => $request->limit_whatsapp_option == 'yes' ? $request->whatsapp_limit : 0,
            'limit_email_option'            => $request->limit_email_option,
            'limit_email_priode'            => $request->limit_email_priode ?? 'daily',
            'email_limit'                   => $request->limit_email_option == 'yes' ? $request->email_limit : 0,
            'limit_scrapp_option'           => $request->limit_scrapp_option,
            'limit_scrapp_priode'           => $request->limit_scrapp_priode ?? 'daily',
            'scrapp_limit'                  => $request->limit_scrapp_option == 'yes' ? $request->scrapp_limit : 0,
            'limit_device'                  => $request->limit_device,
            'device_limit'                  => $request->limit_device == 'yes' ? $request->device_limit : 0,
            'limit_template'                => $request->limit_template,
            'template_limit'                => $request->limit_template == 'yes' ? $request->template_limit : 0,
            'limit_ai_training'             => $request->limit_ai_training,
            'ai_training_limit'             => $request->limit_ai_training == 'yes' ? $request->ai_training_limit : 0,
            'limit_chatbot'                 => $request->limit_chatbot,
            'chatbot_limit'                 => $request->limit_chatbot == 'yes' ? $request->chatbot_limit : 0,
            'ai_response'                   => (int)$request->ai_response,
            'livechat_limit'                => $request->livechat_limit,
            'limit_livechat'                => $request->livechat_limit == 'yes' ? $request->limit_livechat : 0,
            'cek_ongkir'                    => $request->cek_ongkir,
            'google_sheet'                  => $request->google_sheet,
            'limit_instagram'               => $request->limit_instagram ?? 'no',
            'instagram'                     => $request->limit_instagram == 'yes' ? $request->instagram : 0,
            'limit_messanger'               => $request->limit_messanger ?? 'no',
            'messanger'                     => $request->limit_messanger == 'yes' ? $request->messanger : 0,
            'limit_waba'                    => $request->limit_waba ?? 'no',
            'waba_limit'                    => $request->limit_waba == 'yes' ? $request->waba_limit : 0,
            'limit_telegram'                => $request->limit_telegram ?? 'no',
            'telegram'                      => $request->limit_telegram == 'yes' ? $request->telegram : 0,
            'storage'                       => $request->storage ?? 0,
            'mua_limit'                     => $request->mua_limit_optin == 'yes' ? $request->mua_limit : 0,
            'mua_limit_optin'               => $request->mua_limit_optin,
            'max_per_upload'                => $request->max_per_upload,
            'max_total_rag'                 => $request->max_total_rag
        ]);
    }

    public function updateData(Request $request, Package $package)
    {
        $package->update([
            'name'                          => $request->name,
            'price'                         => $request->trial_version == 'yes' ? 0 : $request->price,
            'add_days'                      => $request->days_option == 'limited' ? $request->add_days : 0,
            'days_option'                   => $request->days_option,
            'trial_version'                 => $request->trial_version,
            'limit_user_option'             => $request->limit_user_option,
            'users_limit'                   => $request->limit_user_option == 'yes' ? $request->users_limit : 0,
            'limit_whatsapp_option'         => $request->limit_whatsapp_option,
            'limit_whatsapp_priode'         => $request->limit_whatsapp_priode ?? 'daily',
            'whatsapp_limit'                => $request->limit_whatsapp_option == 'yes' ? $request->whatsapp_limit : 0,
            'limit_email_option'            => $request->limit_email_option,
            'limit_email_priode'            => $request->limit_email_priode ?? 'daily',
            'email_limit'                   => $request->limit_email_option == 'yes' ? $request->email_limit : 0,
            'limit_scrapp_option'           => $request->limit_scrapp_option,
            'limit_scrapp_priode'           => $request->limit_scrapp_priode ?? 'daily',
            'scrapp_limit'                  => $request->limit_scrapp_option == 'yes' ? $request->scrapp_limit : 0,
            'limit_device'                  => $request->limit_device,
            'device_limit'                  => $request->limit_device == 'yes' ? $request->device_limit : 0,
            'limit_template'                => $request->limit_template,
            'template_limit'                => $request->limit_template == 'yes' ? $request->template_limit : 0,
            'limit_ai_training'             => $request->limit_ai_training,
            'ai_training_limit'             => $request->limit_ai_training == 'yes' ? $request->ai_training_limit : 0,
            'limit_chatbot'                 => $request->limit_chatbot,
            'chatbot_limit'                 => $request->limit_chatbot == 'yes' ? $request->chatbot_limit : 0,
            'ai_response'                   => (int)$request->ai_response,
            'livechat_limit'                => $request->livechat_limit,
            'limit_livechat'                => $request->livechat_limit == 'yes' ? $request->limit_livechat : 0,
            'cek_ongkir'                    => $request->cek_ongkir,
            'google_sheet'                  => $request->google_sheet,
            'limit_instagram'               => $request->limit_instagram ?? 'no',
            'instagram'                     => $request->limit_instagram == 'yes' ? $request->instagram : 0,
            'limit_messanger'               => $request->limit_messanger ?? 'no',
            'messanger'                     => $request->limit_messanger == 'yes' ? $request->messanger : 0,
            'limit_waba'                    => $request->limit_waba ?? 'no',
            'waba_limit'                    => $request->limit_waba == 'yes' ? $request->waba_limit : 0,
            'limit_telegram'                => $request->limit_telegram ?? 'no',
            'telegram'                      => $request->limit_telegram == 'yes' ? $request->telegram : 0,
            'storage'                       => $request->storage ?? 0,
            'mua_limit'                     => $request->mua_limit_optin == 'yes' ? $request->mua_limit : 0,
            'mua_limit_optin'               => $request->mua_limit_optin,
            'max_per_upload'                => $request->max_per_upload,
            'max_total_rag'                 => $request->max_total_rag
        ]);
    }

    public function createStorage(Request $request)
    {
        return Package::create([
            'name'                          => $request->name,
            'type'                          => 'storage',
            'percentase_affiliate'          => $request->percentase_affiliate ?? 0,
            'price'                         => $request->price,
            'add_days'                      => $request->days_option == 'limited' ? $request->add_days : 0,
            'days_option'                   => $request->days_option,
            'trial_version'                 => 'no',
            'storage'                       => $request->storage ?? 0
        ]);
    }

    public function updateStorage(Request $request, Package $package)
    {
        $package->update([
            'name'                          => $request->name,
            'percentase_affiliate'          => $request->percentase_affiliate ?? 0,
            'price'                         => $request->price,
            'add_days'                      => $request->days_option == 'limited' ? $request->add_days : 0,
            'days_option'                   => $request->days_option,
            'trial_version'                 => 'no',
            'storage'                       => $request->storage ?? 0
        ]);
    }


    public function createDetails(Request $request, Package $package)
    {

        $package->details()->delete();

        $i  = 0;
        while ($i < count($request->name)) {
            PackageDetail::create([
                'package_id'        => $package->id,
                'name'              => $request->detail_name[$i],
                'status'            => $request->detail_status[$i],
            ]);

            $i++;
        }
    }


    public function deleteData(Package $package)
    {
        $package->details()->delete();
        $package->delete();
    }
}
