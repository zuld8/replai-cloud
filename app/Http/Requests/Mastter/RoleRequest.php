<?php

namespace App\Http\Requests\Mastter;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
     public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('role');
        $merchantId = auth()->user()->merchant_id ?? my_user()?->merchant_id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')
                    ->where('merchant_id', $merchantId)
                    ->ignore($roleId),
            ], 
            'description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama role harus diisi',
            'name.unique' => 'Nama role sudah digunakan',
            'name.max' => 'Nama role maksimal 255 karakter',
        ];
    }

}
