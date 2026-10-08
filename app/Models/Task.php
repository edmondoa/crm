<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'task_code',
        'customer_id',
        'contact_id',
        'property_id',
        'property_location_id',
        'task_type',
        'subject',
        'description',
        'due_at',
        'completed_at',
        'priority',
        'status',
        'outcome',
        'notes',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'completed_at' => 'datetime',
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
                ->where('task_code', 'like', "%{$search}%")
                ->orWhere('subject', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('outcome', 'like', "%{$search}%")
                ->orWhereHas('customer', function ($customerQuery) use ($search) {
                    $customerQuery
                        ->where('customer_code', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                })
                ->orWhereHas('contact', function ($contactQuery) use ($search) {
                    $contactQuery
                        ->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
        });
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', [
            'pending',
            'in_progress',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getTaskTypeLabelAttribute(): string
    {
        return match ($this->task_type) {
            'follow_up' => 'Follow Up',
            'site_visit' => 'Site Visit',
            default => ucwords(
                str_replace('_', ' ', $this->task_type)
            ),
        };
    }

    public function getTaskTypeIconAttribute(): string
    {
        return match ($this->task_type) {
            'follow_up' => 'bi-arrow-repeat',
            'call' => 'bi-telephone',
            'email' => 'bi-envelope',
            'meeting' => 'bi-calendar-event',
            'site_visit' => 'bi-geo-alt',
            'estimate' => 'bi-calculator',
            'proposal' => 'bi-file-earmark-text',
            'document' => 'bi-file-earmark',
            default => 'bi-check2-square',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'in_progress' => 'In Progress',
            default => ucwords(
                str_replace('_', ' ', $this->status)
            ),
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return ucfirst($this->priority);
    }

    public function getIsOverdueAttribute(): bool
    {
        if (!$this->due_at) {
            return false;
        }

        if (in_array($this->status, [
            'completed',
            'cancelled',
        ])) {
            return false;
        }

        return $this->due_at->isPast();
    }
}