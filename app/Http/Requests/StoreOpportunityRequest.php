<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOpportunityRequest extends FormRequest
{
    // public function authorize(): bool
    // {
    //     return $this->user() !== null;
    // }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'lead_id' => [
                'nullable', 'integer', 'exists:leads,id',
            ],
            'customer_id' => [
                'nullable', 'integer', 'exists:customers,id',
            ],
            'assigned_to' => [
                'nullable', 'integer', 'exists:users,id',
            ],
            'stage' => [
                'required',
                Rule::in([
                    'qualification', 'needs_analysis',
                    'proposal', 'negotiation', 'won', 'lost',
                ]),
            ],
            'estimated_value' => [
                'required', 'numeric', 'min:0', 'decimal:0,2',
            ],
            'probability' => [
                'required', 'integer', 'between:0,100',
            ],
            'expected_close_date' => [
                'nullable', 'date',
            ],
            'loss_reason' => [
                'nullable', 'string', 'max:1000',
            ],
            'description' => [
                'nullable', 'string', 'max:5000',
            ],
        ];
    }
}
