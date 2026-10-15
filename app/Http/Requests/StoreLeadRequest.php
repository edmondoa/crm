<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadRequest extends FormRequest
{
    // public function authorize(): bool
    // {
    //     return $this->user() !== null;
    // }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'job_title' => ['nullable', 'string', 'max:150'],
            'source' => [
                'required',
                Rule::in([
                    'website', 'referral', 'social_media',
                    'phone', 'walk_in', 'other',
                ]),
            ],
            'status' => [
                'required',
                Rule::in([
                    'new', 'contacted', 'qualified', 'unqualified',
                ]),
            ],
            'rating' => [
                'required',
                Rule::in(['cold', 'warm', 'hot']),
            ],
            'description' => ['nullable', 'string', 'max:5000'],
            'follow_up_at' => ['nullable', 'date'],
            'assigned_to' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
        ];
    }
}
