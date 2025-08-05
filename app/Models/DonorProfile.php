<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonorProfile extends Model
{
    use HasFactory;

    protected $primaryKey = 'donor_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'donor_id',
        'donor_type',
        'total_donations',
        'total_amount',
        'last_donation_date',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'last_donation_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'donor_id', 'user_id');
    }
}space App\Models;

use Illuminate\Database\Eloquent\Model;

class DonorProfile extends Model
{
    //
}
