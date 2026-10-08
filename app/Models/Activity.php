<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    protected $fillable = [
        'customer_id',
        'contact_id',
        'property_id',
        'property_location_id',
        'activity_type',
        'subject',
        'description',
        'scheduled_at',
        'completed_at',
        'status',
        'priority',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function customer(): BelongsTo
    {
        return $this->belongsTo(
            Customer::class
        );
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(
            Contact::class
        );
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(
            Property::class
        );
    }

    public function propertyLocation(): BelongsTo
    {
        return $this->belongsTo(
            PropertyLocation::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
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
                ->where(
                    'subject',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'description',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'activity_type',
                    'like',
                    "%{$search}%"
                )
                ->orWhereHas(
                    'customer',
                    function ($customerQuery) use ($search) {

                        $customerQuery
                            ->where(
                                'company_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'first_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'last_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'customer_code',
                                'like',
                                "%{$search}%"
                            );
                    }
                )
                ->orWhereHas(
                    'contact',
                    function ($contactQuery) use ($search) {

                        $contactQuery
                            ->where(
                                'first_name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'last_name',
                                'like',
                                "%{$search}%"
                            );
                    }
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

    public function scopePlanned(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            'planned'
        );
    }

    public function scopeCompleted(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            'completed'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getActivityTypeLabelAttribute(): string
    {
        return match ($this->activity_type) {

            'site_visit' => 'Site Visit',

            default => ucwords(
                str_replace(
                    '_',
                    ' ',
                    $this->activity_type
                )
            ),
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return ucfirst(
            $this->priority
        );
    }
}