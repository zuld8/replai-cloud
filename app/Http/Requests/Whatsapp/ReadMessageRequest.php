<?php

namespace App\Http\Requests\Whatsapp;

use Illuminate\Foundation\Http\FormRequest;

class ReadMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [ 
            'api_key'               => 'required',
            'chatid'                => 'required',
            'messages'              => 'required|array|min:1',
            'messages.*.remoteJid'  => 'required',
            'messages.*.id'         => 'required'

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
