<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $primaryKey = 'donation_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'donation_id',
        'donor_id',
        'donation_type',
        'amount',
        'items',
        'quantity',
        'payment_method',
        'transaction_id',
        'expiry_date',
        'donation_date',
        'status',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'donation_date' => 'datetime',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function donor()
    {
        return $this->belongsTo(User::class, 'donor_id', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'donor_id', 'user_id');
    }
    
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }
    
    public function getFormattedAmountAttribute()
    {
        return $this->amount ? '৳' . number_format($this->amount, 2) : 'N/A';
    }
}
