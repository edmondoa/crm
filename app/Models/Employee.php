<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'employee_no',

        'first_name',
        'middle_name',
        'last_name',
        'suffix',

        'email',
        'phone',

        'position',
        'department',
        'employment_type',
        'hire_date',
        'hourly_rate',
        'status',

        'address',

        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',

        'notes',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'hourly_rate' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Job Assignments
    |--------------------------------------------------------------------------
    */

    public function jobAssignments(): HasMany
    {
        return $this->hasMany(JobAssignment::class);
    }

    public function jobSchedules(): HasMany
    {
        return $this->hasMany(JobSchedule::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute(): string
    {
        return trim(
            collect([
                $this->first_name,
                $this->middle_name,
                $this->last_name,
                $this->suffix,
            ])
                ->filter()
                ->implode(' ')
        );
    }

    public function getInitialsAttribute(): string
    {
        $first = $this->first_name
            ? strtoupper(substr($this->first_name, 0, 1))
            : '';

        $last = $this->last_name
            ? strtoupper(substr($this->last_name, 0, 1))
            : '';

        return $first . $last;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}