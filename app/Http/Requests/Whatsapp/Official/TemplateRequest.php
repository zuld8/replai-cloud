<?php

namespace App\Http\Requests\Whatsapp\Official;

use Illuminate\Foundation\Http\FormRequest;

class TemplateRequest extends FormRequest
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
            'name'              => 'required',
            'type'              => 'required|in:text,video,image,document',
            'category'          => 'required|in:UTILITY,MARKETING',
            'lang'              => 'required',
            'body_message'      => 'required|max:1098',
            'variables'         => 'array',
            'buttons'           => 'array',
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
