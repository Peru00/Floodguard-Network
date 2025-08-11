<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributionRecord extends Model
{
    use HasFactory;

    protected $table = 'distribution_records';
    protected $primaryKey = 'distribution_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'distribution_id',
        'distribution_date',
        'volunteer_id',
        'victim_id',
        'inventory_id',
        'location',
        'quantity'
    ];

    protected $dates = [
        'distribution_date'
    ];

    protected $casts = [
        'distribution_date' => 'datetime',
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
