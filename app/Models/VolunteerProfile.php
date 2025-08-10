<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerProfile extends Model
{
    use HasFactory;

    protected $primaryKey = 'volunteer_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'volunteer_id',
        'skill_type',
        'location',
        'is_available',
        'people_helped',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'volunteer_id', 'user_id');
    }
}
