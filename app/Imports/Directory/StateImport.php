<?php

namespace App\Imports\Directory;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StateImport implements ToModel, WithHeadingRow, WithValidation
{
    /** 
     */
    public function rules(): array
    {
        return [
            'name'          => 'required', 
        ];
    }

    public function model(array $rows)
    {
    }
}
