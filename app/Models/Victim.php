<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Victim extends Model
{
    use HasFactory;

    protected $primaryKey = 'victim_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'victim_id',
        'name',
        'age',
        'gender',
        'phone',
        'location',
        'needs',
        'priority',
        'status',
        'registration_date'
    ];

    protected $dates = [
        'registration_date'
    ];

    protected $casts = [
        'registration_date' => 'datetime',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'victim_id', 'victim_id');
    }

    public function distributionRecords()
    {
        return $this->hasMany(DistributionRecord::class, 'victim_id', 'victim_id');
    }
}
