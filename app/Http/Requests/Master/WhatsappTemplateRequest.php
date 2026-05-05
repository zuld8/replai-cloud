<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class WhatsappTemplateRequest extends FormRequest
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
        $rules = [
            'name'                  => 'required',
            'type_content'          => 'required|in:description,button,list,location,vote',
            'media_type'            => 'required|in:text,image,audio,video,document',
            'body_message'          => 'required_if:type_content,description,button,list',
            'buttons'               => 'required_if:type_content,button', 

            'list.title'            => 'required_if:type_content,list',
            'list.button_name'      => 'required_if:type_content,list',
            'list.sections'         => 'required_if:type_content,list',
            'list.sections.*.title' => 'required_if:type_content,list',
            'list.sections.*.rows'  => 'required_if:type_content,list',

            'options'               => 'required_if:type_content,vote',
            'options.*.name'        => 'required_if:type_content,vote',

            'long'                  => 'required_if:type_content,location',
            'lang'                  => 'required_if:type_content,location',
        ];

        // Tambahkan aturan dinamis untuk 'files'
        if ($this->input('media_type') === 'image') {
            $rules['image'] = 'nullable|mimes:png,jpg,jpeg|max:3072';
        } elseif ($this->input('media_type') === 'audio') {
            $rules['image'] = 'nullable|mimes:mp3,wav,ogg|max:3072';
        } elseif ($this->input('media_type') === 'video') {
            $rules['image'] = 'nullable|mimes:mp4,mkv';
        } elseif ($this->input('media_type') === 'document') {
            $rules['image'] = 'nullable|mimes:pdf,doc,docx|max:3072';
        }

        return $rules;
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {

        $response = response()->json([
            'message' => $validator->errors()->first(),
        ], 419);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
