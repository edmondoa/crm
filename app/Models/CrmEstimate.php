<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmEstimate extends Model
{
    protected $table = 'crm_estimates';

    protected $fillable = [
        'estimate_number',
        'customer_id',
        'property_id',
        'job_id',
        'estimate_date',
        'expiration_date',
        'status',
        'subtotal',
        'discount',
        'tax',
        'other_charges',
        'grand_total',
        'notes',
        'terms',
    ];

    protected $casts = [
        'estimate_date' => 'date',
        'expiration_date' => 'date',

        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'other_charges' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(
            CrmEstimateItem::class,
            'estimate_id'
        )->orderBy('sort_order');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'estimate_id');
    }

    public function recalculateTotals(): void
    {
        $subtotal = $this->items()->sum('total');

        $grandTotal =
            $subtotal
            - $this->discount
            + $this->tax
            + $this->other_charges;

        $this->update([
            'subtotal' => $subtotal,
            'grand_total' => max(0, $grandTotal),
        ]);
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isExpired(): bool
    {
        return $this->expiration_date
            && $this->expiration_date->isPast()
            && !in_array($this->status, [
                'accepted',
                'rejected',
                'converted',
            ]);
    }
}