<?php

namespace App\Http\Requests\Mastter;

use Illuminate\Foundation\Http\FormRequest;

class AssignPermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'required|uuid|exists:permissions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'permission_ids.required' => 'Permission harus dipilih',
            'permission_ids.array' => 'Format permission tidak valid',
            'permission_ids.*.uuid' => 'ID permission tidak valid',
            'permission_ids.*.exists' => 'Permission tidak ditemukan',
        ];
    }
}
