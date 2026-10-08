<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'customer_id',
        'property_id',
        'job_id',
        'estimate_id',
        'invoice_date',
        'due_date',
        'status',
        'subtotal',
        'discount',
        'tax',
        'other_charges',
        'grand_total',
        'amount_paid',
        'balance_due',
        'notes',
        'terms',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',

        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'other_charges' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
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
        return $this->belongsTo(Job::class);
    }

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(
            CrmEstimate::class,
            'estimate_id'
        );
    }

    public function items(): HasMany
    {
        return $this->hasMany(
            InvoiceItem::class,
            'invoice_id'
        )->orderBy('sort_order');
    }

    public function recalculateTotals(): void
    {
        $this->loadMissing('items');

        $subtotal = $this->items->sum('line_subtotal');
        $discount = $this->items->sum('discount_amount');
        $tax = $this->items->sum('tax_amount');

        $grandTotal =
            $subtotal
            - $discount
            + $tax
            + (float) $this->other_charges;

        $amountPaid = (float) $this->amount_paid;

        $this->subtotal = $subtotal;
        $this->discount = $discount;
        $this->tax = $tax;
        $this->grand_total = $grandTotal;
        $this->balance_due = max(
            $grandTotal - $amountPaid,
            0
        );

        $this->save();
    }

    public function updatePaymentStatus(): void
    {
        if ($this->status === 'cancelled') {
            return;
        }

        if ($this->amount_paid >= $this->grand_total) {
            $this->status = 'paid';
        } elseif ($this->amount_paid > 0) {
            $this->status = 'partial';
        } elseif (
            $this->due_date &&
            $this->due_date->isPast()
        ) {
            $this->status = 'overdue';
        }

        $this->save();
    }
}