<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmSetting extends Model
{
    protected $fillable = [
        'customer_code_prefix',
        'property_code_prefix',
        'task_code_prefix',
        'job_code_prefix',
        'quotation_code_prefix',
        'lead_code_prefix',
        'opportunity_code_prefix',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    /**
     * Get the active CRM settings.
     */
    public static function active(): self
    {
        return static::firstOrCreate(
            ['enabled' => true],
            [
                'customer_code_prefix' => 'CUST-',
                'property_code_prefix' => 'PROP-',
                'task_code_prefix' => 'TASK-',
                'job_code_prefix' => 'JOB-',
                'quotation_code_prefix' => 'QUO-',
                'lead_code_prefix' => 'LEAD-',
                'opportunity_code_prefix' => 'OPP-',
            ]
        );
    }
}