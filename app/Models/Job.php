<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    protected $fillable = [
        'job_code',
        'work_order_number',

        'customer_id',
        'contact_id',
        'property_id',
        'property_location_id',

        'title',
        'job_type',
        'description',
        'scope_of_work',

        'scheduled_start_at',
        'scheduled_end_at',
        'started_at',
        'completed_at',

        'priority',
        'status',

        'estimated_amount',
        'approved_amount',

        'customer_notes',
        'internal_notes',
    ];

    protected $casts = [
        'scheduled_start_at' => 'datetime',
        'scheduled_end_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',

        'estimated_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function propertyLocation(): BelongsTo
    {
        return $this->belongsTo(
            PropertyLocation::class,
            'property_location_id'
        );
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(JobAssignment::class);
    }

    public function primaryAssignment()
    {
        return $this->hasOne(JobAssignment::class)
            ->where('is_primary', true);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(JobSchedule::class);
    }

    public function estimates(): HasMany
    {
        return $this->hasMany(
            CrmEstimate::class,
            'job_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Future Relationships
    |--------------------------------------------------------------------------
    |
    | Add these when the corresponding phases are implemented:
    |
    | project()
    | constructionSite()
    | employees()
    | laborCosts()
    | invoices()
    |
    |--------------------------------------------------------------------------
    */

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
                ->where('job_code', 'like', "%{$search}%")
                ->orWhere(
                    'work_order_number',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'title',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'description',
                    'like',
                    "%{$search}%"
                )
                ->orWhereHas(
                    'customer',
                    function ($customerQuery) use ($search) {

                        $customerQuery
                            ->where(
                                'customer_code',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
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
                            )
                            ->orWhere(
                                'email',
                                'like',
                                "%{$search}%"
                            );
                    }
                );
        });
    }

    public function scopeOpen(
        Builder $query
    ): Builder {
        return $query->whereIn('status', [
            'draft',
            'scheduled',
            'in_progress',
            'on_hold',
        ]);
    }

    public function scopeActive(
        Builder $query
    ): Builder {
        return $query->whereIn('status', [
            'scheduled',
            'in_progress',
            'on_hold',
        ]);
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

    public function getJobTypeLabelAttribute(): string
    {
        return match ($this->job_type) {
            'site_visit' => 'Site Visit',
            default => ucwords(
                str_replace(
                    '_',
                    ' ',
                    $this->job_type
                )
            ),
        };
    }

    public function getJobTypeIconAttribute(): string
    {
        return match ($this->job_type) {
            'service' => 'bi-tools',
            'repair' => 'bi-wrench-adjustable',
            'maintenance' => 'bi-gear',
            'installation' => 'bi-box-seam',
            'inspection' => 'bi-search',
            'replacement' => 'bi-arrow-repeat',
            'construction' => 'bi-building',
            'renovation' => 'bi-house-gear',
            'emergency' => 'bi-exclamation-triangle',
            default => 'bi-briefcase',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'in_progress' => 'In Progress',
            'on_hold' => 'On Hold',
            default => ucwords(
                str_replace(
                    '_',
                    ' ',
                    $this->status
                )
            ),
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return ucfirst($this->priority);
    }

    public function getIsOverdueAttribute(): bool
    {
        if (!$this->scheduled_end_at) {
            return false;
        }

        if (in_array($this->status, [
            'completed',
            'cancelled',
        ])) {
            return false;
        }

        return $this->scheduled_end_at->isPast();
    }

    public function getDurationInMinutesAttribute(): ?int
    {
        if (
            !$this->scheduled_start_at ||
            !$this->scheduled_end_at
        ) {
            return null;
        }

        return $this->scheduled_start_at
            ->diffInMinutes(
                $this->scheduled_end_at
            );
    }
}