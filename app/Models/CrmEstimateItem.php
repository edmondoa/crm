<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmEstimateItem extends Model
{
    protected $table = 'crm_estimate_items';

    protected $fillable = [
        'estimate_id',
        'item_type',
        'sku',
        'description',
        'quantity',
        'unit',
        'unit_cost',
        'markup_percent',
        'unit_price',
        'tax_percent',
        'tax_amount',
        'total',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_cost' => 'decimal:2',
        'markup_percent' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(
            CrmEstimate::class,
            'estimate_id'
        );
    }

    public function calculateTotals(): void
    {
        $basePrice = $this->unit_cost;

        if ($this->markup_percent > 0) {
            $basePrice +=
                $basePrice * ($this->markup_percent / 100);
        }

        $this->unit_price = $basePrice;

        $lineSubtotal =
            $this->quantity * $this->unit_price;

        $this->tax_amount =
            $lineSubtotal * ($this->tax_percent / 100);

        $this->total =
            $lineSubtotal + $this->tax_amount;
    }
}