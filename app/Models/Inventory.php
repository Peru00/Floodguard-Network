<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';
    protected $primaryKey = 'inventory_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'inventory_id',
        'item_name',
        'category',
        'quantity',
        'unit',
        'source',
        'donation_date',
        'expiry_date',
        'location',
        'status'
    ];

    protected $dates = [
        'donation_date',
        'expiry_date'
    ];

    protected $casts = [
        'donation_date' => 'datetime',
        'expiry_date' => 'datetime',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'inventory_id', 'inventory_id');
    }

    public function distributionRecords()
    {
        return $this->hasMany(DistributionRecord::class, 'inventory_id', 'inventory_id');
    }
}
