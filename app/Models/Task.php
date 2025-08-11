<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = 'distribution_tasks';
    protected $primaryKey = 'task_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'task_id',
        'volunteer_id',
        'inventory_id',
        'victim_id',
        'quantity',
        'location',
        'status',
        'assigned_date',
        'completion_date'
    ];

    protected $dates = [
        'assigned_date',
        'completion_date'
    ];

    protected $casts = [
        'assigned_date' => 'datetime',
        'completion_date' => 'datetime',
    ];

    public function volunteer()
    {
        return $this->belongsTo(User::class, 'volunteer_id', 'user_id');
    }

    public function victim()
    {
        return $this->belongsTo(Victim::class, 'victim_id', 'victim_id');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id', 'inventory_id');
    }
}
