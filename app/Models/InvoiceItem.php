<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'item_type',
        'description',
        'sku',
        'quantity',
        'unit',
        'unit_price',
        'discount_percent',
        'tax_percent',
        'line_subtotal',
        'discount_amount',
        'tax_amount',
        'line_total',
        'sort_order',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'line_subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(
            Invoice::class,
            'invoice_id'
        );
    }

    public function calculateTotals(): void
    {
        $quantity = (float) $this->quantity;
        $unitPrice = (float) $this->unit_price;

        $discountPercent =
            (float) ($this->discount_percent ?? 0);

        $taxPercent =
            (float) ($this->tax_percent ?? 0);

        $this->line_subtotal =
            $quantity * $unitPrice;

        $this->discount_amount =
            $this->line_subtotal *
            ($discountPercent / 100);

        $taxableAmount =
            $this->line_subtotal -
            $this->discount_amount;

        $this->tax_amount =
            $taxableAmount *
            ($taxPercent / 100);

        $this->line_total =
            $taxableAmount +
            $this->tax_amount;
    }
}