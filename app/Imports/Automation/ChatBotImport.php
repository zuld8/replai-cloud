<?php

namespace App\Imports\Automation;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ChatBotImport implements ToModel, WithHeadingRow, WithValidation
{
    /** 
     */
    public function rules(): array
    {
        return [
            'keyword'           => 'required',
            'method'            => 'required|text|template',
            'template'          => 'required_if:method,template',
            'text'              => 'required_if:method,text',
            'device'            => 'required'
        ];
    }

    public function model(array $rows) {}
}
