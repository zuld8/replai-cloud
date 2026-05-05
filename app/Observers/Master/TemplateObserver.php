<?php

namespace App\Observers\Master;

use App\Models\Master\MessageTemplate;
use Illuminate\Http\Request;

class TemplateObserver
{
    public function getData(Request $request)
    {
        return MessageTemplate::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->where(function ($q) use ($request) {
            return $request->waba ? $q->where("meta_account_id", $request->waba) : '';
        })->where(function ($q) use ($request) {
            return $request->type ? $q->where("type", $request->type) : '';
        })->where(function ($q) use ($request) {
            return $request->status ? $q->where("waba_status_template", $request->status) : '';
        })->where('master_type', 'yes')->orderBy('created_at', 'desc');
    }


    public function checkLimit()
    {
        if (my_user()->role == 'user') {
            $templateLimitation  = template_limitation(my_business());
            if (!$templateLimitation) {
                return false;
            }
        }


        return true;
    }

    public function createData(Request $request, String $image)
    {

        $body   = null;

        if ($request->type_content == 'button') {
            $buttons    = array( 
                'footer'    => $request->footer_message,
                'buttons'   => $request->buttons
            );

            $body               = json_encode($buttons);
        }

        if ($request->type_content == 'list') {

            $list       = array( 
                'title'         => $request->list['title'],
                'button_name'   => $request->list['button_name'],
                'footer'        => $request->footer_message,
                'sections'      => $request->list['sections']
            );

            $body               = json_encode($list);
        }

        if ($request->type_content == 'vote') {

            $vote       = array(
                'title'         => $request->list['title'],
                'options'       => $request->options
            );

            $body               = json_encode($vote);
        }

        if ($request->type_content == 'location') {
            $data['longitude']      = $request->long;
            $data['latitude']       = $request->lang;
            $body                   = json_encode($data);
        }

        return MessageTemplate::create([
            'name'              => $request->name,
            'image'             => $image,
            'type'              => 'whatsapp',
            'media_type'        => $request->media_type,
            'type_content'      => $request->type_content,
            'message'           => $request->body_message,
            'button_or_list'    => $body
        ]);
    }

    public function updateData(Request $request, MessageTemplate $template, String $image)
    {

        $body   = null;

        if ($request->type_content == 'button') {
            $buttons    = array( 
                'footer'    => $request->footer_message,
                'buttons'   => $request->buttons
            );

            $body               = json_encode($buttons);
        }

        if ($request->type_content == 'list') {
            $list       = array(
                'title'         => $request->list['title'],
                'button_name'   => $request->list['button_name'],
                'footer'        => $request->footer_message,
                'sections'      => $request->list['sections']
            );

            $body               = json_encode($list);
        }

        if ($request->type_content == 'vote') {

            $vote       = array(
                'title'         => $request->list['title'],
                'options'       => $request->options
            );

            $body               = json_encode($vote);
        }

        if ($request->type_content == 'location') {
            $data['longitude']      = $request->long;
            $data['latitude']       = $request->lang;
            $body                   = json_encode($data);
        }

        $template->update([
            'name'              => $request->name,
            'image'             => $request->media_type != 'text' ? ($image != '' ? $image : $template->image) : null,
            'media_type'        => $request->media_type,
            'type_content'      => $request->type_content,
            'message'           => $request->body_message,
            'button_or_list'    => $body
        ]);
    }

    public function createForEmail(Request $request)
    {
        return MessageTemplate::create([
            'name'              => $request->name,
            'type'              => 'email',
            'message'           => '-'
        ]);
    }

    public function updateForEmail(Request $request, MessageTemplate $template)
    {
        $body       = [
            'components'    => $request->get('laravel-grapesjs-components'),
            'styles'        => $request->get('laravel-grapesjs-styles'),
            'css'           => $request->get('laravel-grapesjs-css'),
            'html'          => $request->get('laravel-grapesjs-html'),
        ]; 

        $template->update([
            'message'    => $body
        ]);
    }

    public function deleteData(MessageTemplate $template)
    {
        $template->delete();
    }
}
