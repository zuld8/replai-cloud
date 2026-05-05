<?php

namespace App\Http\Requests\Whatsapp\Official;

use Illuminate\Foundation\Http\FormRequest;

class ChatbotRequest extends FormRequest
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
            'keyword'               => 'required', 
            'method'                => 'required|in:text,template',
            'template'              => 'required_if:method,template',
            'devices'               => 'required'
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {

        $response = response()->json([
            'message' => $validator->errors()->first(),
        ], 419);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
