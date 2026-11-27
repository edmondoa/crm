<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvoiceRequest extends FormRequest
{
    // public function authorize(): bool
    // {
    //     return true;
    // }

    public function rules(): array
    {
        return [

            'customer_id' => [
                'required',
                'integer',
                'exists:customers,id',
            ],

            'property_id' => [
                'nullable',
                'integer',
                Rule::exists('properties', 'id')
                    ->where('customer_id', $this->input('customer_id')),
            ],

            'job_id' => [
                'nullable',
                'integer',
                Rule::exists('jobs', 'id')
                    ->where('customer_id', $this->input('customer_id')),
            ],

            'estimate_id' => [
                'nullable',
                'integer',
                Rule::exists('crm_estimates', 'id')
                    ->where('customer_id', $this->input('customer_id')),
            ],

            'invoice_date' => [
                'required',
                'date',
            ],

            'due_date' => [
                'required',
                'date',
                'after_or_equal:invoice_date',
            ],

            'status' => [
                'required',
                Rule::in([
                    'draft',
                    'sent',
                    'viewed',
                    'partial',
                    'paid',
                    'overdue',
                    'cancelled',
                ]),
            ],

            'other_charges' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'terms' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.item_type' => [
                'required',
                Rule::in([
                    'material',
                    'labor',
                    'service',
                    'equipment',
                    'other',
                ]),
            ],

            'items.*.description' => [
                'required',
                'string',
                'max:500',
            ],

            'items.*.sku' => [
                'nullable',
                'string',
                'max:100',
            ],

            'items.*.quantity' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'items.*.unit' => [
                'required',
                'string',
                'max:50',
            ],

            'items.*.unit_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'items.*.discount_percent' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            'items.*.tax_percent' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            'items.*.notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }
}
