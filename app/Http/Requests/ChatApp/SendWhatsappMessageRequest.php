<?php

namespace App\Http\Requests\ChatApp;

use Illuminate\Foundation\Http\FormRequest;

class SendWhatsappMessageRequest extends FormRequest
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
            'phone'         => 'required',
            'type'          => 'required|in:text,media,location,document,photo',
            'text'          => 'required_if:type,text',
            'file'          => 'required_if:type,media,document|mimes:jpeg,jpg,png,webp,pdf,docx,csv,xlsx,mp4,mp3,ppt|max:3072',
            'photo'         => 'required_if:type,photo'
            
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
