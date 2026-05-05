<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class CreateTicketRequest extends FormRequest
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
            'contacts.id' => 'required|uuid|exists:stores,id',
            'agents'        => 'required|array|min:1',
            'agents.*.id'   => 'uuid|exists:users,id',
            'label_id' => 'required|uuid|exists:labels,id',
            'category_id' => 'required|uuid|exists:ticket_categories,id', 

            'ticket_name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'ticket_level' => 'required|in:low,medium,high,urgent',
            'notes' => 'nullable|string',
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
