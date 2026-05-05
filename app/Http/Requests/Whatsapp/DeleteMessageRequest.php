<?php

namespace App\Http\Requests\Whatsapp;

use Illuminate\Foundation\Http\FormRequest;

class DeleteMessageRequest extends FormRequest
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
            'message.key.remoteJid' => 'required',
            'message.key.fromMe'    => 'required|boolean',
            'message.key.id'        => 'required',
            'message.deleteMedia'   => 'required|boolean',
            'message.timestamp'     => 'required'

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
