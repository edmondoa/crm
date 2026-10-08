<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCrmEstimateRequest extends FormRequest
{
    

    public function rules(): array
    {
        return [

            /*
             * Estimate
             */
            'estimate_number' => [
                'nullable',
                'string',
                'max:50',
                'unique:crm_estimates,estimate_number',
            ],

            'customer_id' => [
                'required',
                'integer',
                'exists:customers,id',
            ],

            'property_id' => [
                'nullable',
                'integer',
                'exists:properties,id',
            ],

            'job_id' => [
                'nullable',
                'integer',
                'exists:jobs,id',
            ],

            'estimate_date' => [
                'required',
                'date',
            ],

            /*
             * IMPORTANT:
             * Your controller uses expiration_date,
             * so the form/request should use expiration_date.
             */
            'expiration_date' => [
                'nullable',
                'date',
                'after_or_equal:estimate_date',
            ],

            'status' => [
                'nullable',
                Rule::in([
                    'draft',
                    'sent',
                    'accepted',
                    'rejected',
                    'expired',
                    'converted',
                ]),
            ],

            'discount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'tax' => [
                'nullable',
                'numeric',
                'min:0',
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

            /*
             * Estimate Items
             */
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

            /*
             * Your form submits unit_cost,
             * NOT unit_price.
             */
            'items.*.unit_cost' => [
                'required',
                'numeric',
                'min:0',
            ],

            'items.*.markup_percent' => [
                'nullable',
                'numeric',
                'min:0',
                'max:1000',
            ],

            /*
             * This is calculated by JavaScript.
             * We don't need to trust it.
             */
            'items.*.unit_price' => [
                'nullable',
                'numeric',
                'min:0',
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

    public function messages(): array
    {
        return [

            'customer_id.required' =>
                'Please select a customer.',

            'customer_id.exists' =>
                'The selected customer is invalid.',

            'estimate_date.required' =>
                'Please enter the estimate date.',

            'expiration_date.after_or_equal' =>
                'The expiration date must be on or after the estimate date.',

            'items.required' =>
                'Please add at least one estimate item.',

            'items.min' =>
                'Please add at least one estimate item.',

            'items.*.item_type.required' =>
                'Please select an item type.',

            'items.*.description.required' =>
                'Please enter an item description.',

            'items.*.quantity.required' =>
                'Please enter a quantity.',

            'items.*.quantity.gt' =>
                'Quantity must be greater than zero.',

            'items.*.unit.required' =>
                'Please enter a unit.',

            'items.*.unit_cost.required' =>
                'Please enter the unit cost.',

            'items.*.unit_cost.min' =>
                'Unit cost cannot be negative.',

            'items.*.markup_percent.max' =>
                'Markup percentage cannot exceed 1000%.',

            'items.*.tax_percent.max' =>
                'Tax percentage cannot exceed 100%.',
        ];
    }
}