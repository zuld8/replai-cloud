<?php

namespace App\Observers\Saas;

use App\Models\InternalSetting;
use Illuminate\Http\Request;

class InternalSettingObserver
{
    public function generalSetting()
    {
        return InternalSetting::first(['app_name', 'logo', 'white_logo', 'icon', 'tax', 'email_contact', 'phone_contact', 'id', 'contact_address', 'loader', 'currency', 'currency_position', 'fb_app_id', 'fb_app_secret', 'fb_config_id']);
    }

    public function webSetting()
    {
        return InternalSetting::first(['meta_keyword', 'meta_description', 'register', 'frontend', 'blog', 'pricing', 'contact', 'copyright', 'footer_description', 'id', 'footer_web', 'web_template', 'footer_1', 'footer_2', 'footer_3', 'logo', 'app_name', 'icon']);
    }

    public function generalUpdate(Request $request, String $whiteLogo, String $logo, String $icon, String $loader)
    {
        $settings   = $this->generalSetting();
        $settings->update([
            'app_name'              => $request->name,
            'logo'                  => $logo != '' ? $logo : $settings->logo,
            'white_logo'            => $whiteLogo != '' ? $whiteLogo : $settings->white_logo,
            'icon'                  => $icon != '' ? $icon : $settings->icon,
            'loader'                => $loader != '' ? $loader : $settings->loader,
            'tax'                   => $request->tax,
            'email_contact'         => $request->email,
            'phone_contact'         => $request->phone,
            'contact_address'       => $request->address,
            'currency'              => $request->currency,
            'currency_position'     => $request->currency_position

        ]);
    }

    public function webUpdate(Request $request)
    {
        $this->webSetting()->update([
            'meta_keyword'          => $request->keyword,
            'meta_description'      => $request->description,
            'register'              => !empty($request->register) ? 'yes' : 'no',
            'frontend'              => !empty($request->frontend) ? 'yes' : 'no',
            'blog'                  => !empty($request->blog) ? 'yes' : 'no',
            'pricing'               => !empty($request->pricing) ? 'yes' : 'no',
            'contact'               => !empty($request->contact) ? 'yes' : 'no',
            'copyright'             => $request->copyright,
            'footer_description'    => $request->footer,
            'web_template'          => $request->web_template,
            'footer_web'            => $request->footer_web,
            'footer_1'              => $request->footer_1,
            'footer_2'              => $request->footer_2,
            'footer_3'              => $request->footer_3
        ]);
    }
}
