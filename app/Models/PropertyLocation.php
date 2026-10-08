<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyLocation extends Model
{
    protected $fillable = [
        'property_id',
        'location_code',
        'location_name',
        'location_type',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'zip_code',
        'county',
        'latitude',
        'longitude',
        'is_primary',
        'status',
        'notes',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_primary' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function property(): BelongsTo
    {
        return $this->belongsTo(
            Property::class
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
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            'active'
        );
    }

    public function scopeSearch(
        Builder $query,
        ?string $search
    ): Builder {

        if (!$search) {
            return $query;
        }

        return $query->where(function ($query) use ($search) {

            $query
                ->where(
                    'location_code',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'location_name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'address_line_1',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'address_line_2',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'city',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'state',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'zip_code',
                    'like',
                    "%{$search}%"
                )
                ->orWhereHas(
                    'property',
                    function ($propertyQuery) use ($search) {

                        $propertyQuery
                            ->where(
                                'name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'property_code',
                                'like',
                                "%{$search}%"
                            );
                    }
                );
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getLocationTypeLabelAttribute(): string
    {
        return match ($this->location_type) {

            'job_site' =>
                'Job Site',

            'multi_family' =>
                'Multi-Family',

            default =>
                ucwords(
                    str_replace(
                        '_',
                        ' ',
                        $this->location_type
                    )
                ),
        };
    }

    public function getFullAddressAttribute(): string
    {
        return trim(
            implode(
                ', ',
                array_filter([
                    $this->address_line_1,
                    $this->address_line_2,
                    $this->city,
                    $this->state . ' ' . $this->zip_code,
                ])
            )
        );
    }

    public function getShortAddressAttribute(): string
    {
        return trim(
            implode(
                ', ',
                array_filter([
                    $this->city,
                    $this->state . ' ' . $this->zip_code,
                ])
            )
        );
    }
}