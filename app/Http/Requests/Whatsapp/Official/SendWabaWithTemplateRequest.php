<?php

namespace App\Http\Requests\Whatsapp\Official;

use Illuminate\Foundation\Http\FormRequest;

class SendWabaWithTemplateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'api_key'           => 'required|string',
            'device_key'        => 'nullable|string', 
            'phone'             => 'required|string',
            'template_id'       => 'required|string',
            'template_lang'     => 'required|string', 

            // Parameters untuk template
            'header'            => 'nullable|array',
            'header.type'       => 'nullable|in:text,image,video,document',
            'header.value'      => 'nullable|string',

            'body'              => 'nullable|array',
            'body.*'            => 'nullable|string', 

            'buttons'           => 'nullable|array',
            'buttons.*.type'    => 'nullable|in:url,quick_reply',
            'buttons.*.index'   => 'required_with:buttons|integer',
            'buttons.*.value'   => 'required_with:buttons|string', 
        ];
    }

    public function messages()
    {
        return [
            'api_key.required'          => 'API Key wajib diisi',
            'phone.required'            => 'Nomor telepon wajib diisi',
            'template_id.required'      => 'Id template wajib diisi',
            'template_lang.required'    => 'Bahasa template wajib diisi',
            'header.type.in'            => 'Header type harus: text, image, video, atau document',
            'buttons.*.type.in'         => 'Button type harus: url atau quick_reply',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {

        $response = response()->json([
            'message' => $validator->errors()->first(),
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
