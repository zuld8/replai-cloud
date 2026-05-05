<?php

namespace App\Imports\Store;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StoreImport implements ToModel, WithHeadingRow, WithValidation
{
     /** 
     */
    public function rules(): array
    {
        return [
            'name'          => 'required', 
            'category'      => 'required',   
        ];
    }

    public function model(array $rows)
    {
    }
}
