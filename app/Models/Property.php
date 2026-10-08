<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    protected $fillable = [
        'customer_id',
        'primary_contact_id',
        'property_code',
        'name',
        'property_type',
        'description',
        'status',
        'notes',
    ];

    /**
     * Customer that owns this property.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Primary contact for this property.
     */
    public function primaryContact(): BelongsTo
    {
        return $this->belongsTo(
            Contact::class,
            'primary_contact_id'
        );
    }

    public function locations(): HasMany
    {
        return $this->hasMany(
            PropertyLocation::class
        );
    }

    public function activities(): HasMany
    {
        return $this->hasMany(
            Activity::class
        );
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function estimates()
    {
        return $this->hasMany(
            CrmEstimate::class,
            'property_id'
        );
    }

    /**
     * Search properties.
     */
    public function scopeSearch(
        Builder $query,
        ?string $search
    ): Builder {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($query) use ($search) {

            $query
                ->where('property_code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('property_type', 'like', "%{$search}%")
                ->orWhereHas('customer', function ($customerQuery) use ($search) {

                    $customerQuery
                        ->where('company_name', 'like', "%{$search}%")
                        ->orWhere('customer_code', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");

                });
        });
    }

    /**
     * Active properties.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Human-readable property type.
     */
    public function getPropertyTypeLabelAttribute(): string
    {
        return match ($this->property_type) {

            'multi_family' => 'Multi-Family',

            default => ucwords(
                str_replace('_', ' ', $this->property_type)
            ),
        };
    }
}