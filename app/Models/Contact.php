<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    protected $fillable = [
        'customer_id',
        'contact_type',

        'first_name',
        'middle_name',
        'last_name',

        'job_title',
        'department',

        'email',
        'phone',
        'mobile',

        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'zip_code',
        'country',

        'is_primary',
        'preferred_contact_method',

        'notes',
        'status',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Customer that owns this contact.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
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

    /**
     * Contact's full name.
     */
    public function getDisplayNameAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ]))) ?: 'Unnamed Contact';
    }

    /**
     * Full US address.
     */
    public function getFullAddressAttribute(): string
    {
        return trim(implode(', ', array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->state
                ? $this->state . ' ' . $this->zip_code
                : $this->zip_code,
            $this->country,
        ])));
    }

    /**
     * Search contacts.
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
                ->where('first_name', 'like', "%{$search}%")
                ->orWhere('middle_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('job_title', 'like', "%{$search}%")
                ->orWhere('department', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('mobile', 'like', "%{$search}%")
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
     * Active contacts.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}