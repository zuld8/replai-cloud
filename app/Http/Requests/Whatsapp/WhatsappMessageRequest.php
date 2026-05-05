<?php

namespace App\Http\Requests\Whatsapp;

use Illuminate\Foundation\Http\FormRequest;

class WhatsappMessageRequest extends FormRequest
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
            'phone'             => 'required', 
            'api_key'           => 'required',
            'method'            => 'required|in:template,text',
            'template'          => 'required_if:method,template',
            'text'              => 'required_if:method,text'
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
