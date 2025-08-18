<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VolunteerTask extends Model
{
    use HasFactory;

    protected $table = 'volunteer_tasks';
    protected $primaryKey = 'task_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'task_id',
        'volunteer_id',
        'victim_id',
        'assigned_by',
        'title',
        'description',
        'priority',
        'task_type',
        'status',
        'location',
        'due_date',
        'assigned_date',
        'completed_date',
        'notes'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'assigned_date' => 'datetime',
        'completed_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($task) {
            if (empty($task->task_id)) {
                $task->task_id = 'TASK-' . strtoupper(Str::random(8));
            }
        });
    }

    public function volunteer()
    {
        return $this->belongsTo(User::class, 'volunteer_id', 'user_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by', 'user_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForVolunteer($query, $volunteerId)
    {
        return $query->where('volunteer_id', $volunteerId);
    }
}
