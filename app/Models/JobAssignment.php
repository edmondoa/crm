<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobAssignment extends Model
{
    protected $fillable = [
        'job_id',
        'employee_id',

        'assignment_role',
        'is_primary',

        'status',

        'assigned_at',
        'started_at',
        'completed_at',

        'estimated_hours',
        'actual_hours',
        'hourly_rate',

        'notes',
    ];

    protected $casts = [
        'is_primary' => 'boolean',

        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',

        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Job
    |--------------------------------------------------------------------------
    */

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Employee
    |--------------------------------------------------------------------------
    */

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(JobSchedule::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getEstimatedCostAttribute(): float
    {
        return (float) (
            ($this->estimated_hours ?? 0) *
            ($this->hourly_rate ?? 0)
        );
    }

    public function getActualCostAttribute(): float
    {
        return (float) (
            ($this->actual_hours ?? 0) *
            ($this->hourly_rate ?? 0)
        );
    }
}