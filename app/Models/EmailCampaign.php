<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailCampaign extends Model
{
    public const CAMPAIGN_TYPES = [
        'general' => 'General audience',
        'birthday' => 'Birthday',
        'new_customers' => 'New customers added',
        'pending_invoices' => 'Pending invoices',
        'monthly_invoices' => 'Monthly invoices',
    ];

    protected $fillable = [
        'name',
        'email_template_id',
        'target_type',
        'audience_description',
        'status',
        'scheduled_at',
        'last_run_at',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'last_run_at' => 'datetime',
    ];

    public function emailTemplate(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    public function getCampaignTypeLabelAttribute(): string
    {
        return self::CAMPAIGN_TYPES[$this->target_type] ?? self::CAMPAIGN_TYPES['general'];
    }
}
