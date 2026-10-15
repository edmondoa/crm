<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Opportunity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'opportunity_number',
        'name',
        'lead_id',
        'customer_id',
        'assigned_to',
        'stage',
        'estimated_value',
        'probability',
        'expected_close_date',
        'loss_reason',
        'description',
        'created_by',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'estimated_value' => 'decimal:2',
            'probability' => 'integer',
            'expected_close_date' => 'date',
            'closed_at' => 'datetime',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getWeightedValueAttribute(): float
    {
        return round(
            (float) $this->estimated_value
            * $this->probability / 100,
            2
        );
    }
}
