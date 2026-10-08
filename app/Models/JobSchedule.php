<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'job_assignment_id',
        'employee_id',
        'scheduled_date',
        'start_time',
        'end_time',
        'status',
        'location',
        'notes',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function jobAssignment(): BelongsTo
    {
        return $this->belongsTo(JobAssignment::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->whereIn('status', [
            'scheduled',
            'confirmed',
            'in_progress',
        ]);
    }

    public function scopeForDate(Builder $query, $date): Builder
    {
        return $query->whereDate('scheduled_date', $date);
    }

    public function scopeForEmployee(
        Builder $query,
        int $employeeId
    ): Builder {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeForJob(
        Builder $query,
        int $jobId
    ): Builder {
        return $query->where('job_id', $jobId);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getTimeRangeAttribute(): string
    {
        return date(
            'g:i A',
            strtotime($this->start_time)
        ) . ' - ' . date(
            'g:i A',
            strtotime($this->end_time)
        );
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'scheduled'   => 'Scheduled',
            'confirmed'   => 'Confirmed',
            'in_progress' => 'In Progress',
            'completed'   => 'Completed',
            'cancelled'   => 'Cancelled',
            default       => ucfirst($this->status),
        };
    }
}