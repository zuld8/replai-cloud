<?php

namespace App\Imports\Directory;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CityImport implements ToModel, WithHeadingRow, WithValidation
{
    /** 
     */
    public function rules(): array
    {
        return [
            'name'          => 'required', 
            'type'          => 'required',
            'state_id'      => 'required'
        ];
    }

    public function model(array $rows)
    {
    }
}
