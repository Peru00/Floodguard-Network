<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    // Override the getAuthIdentifierName method for Laravel auth
    public function getAuthIdentifierName()
    {
        return 'user_id';
    }
    
    // Override the getAuthIdentifier method
    public function getAuthIdentifier()
    {
        return $this->getAttribute($this->getAuthIdentifierName());
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'role',
        'profile_image',
        'registration_date',
        'last_login',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'registration_date' => 'datetime',
            'last_login' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the volunteer profile for the user.
     */
    public function volunteerProfile()
    {
        return $this->hasOne(VolunteerProfile::class, 'volunteer_id', 'user_id');
    }

    /**
     * Get the donor profile for the user.
     */
    public function donorProfile()
    {
        return $this->hasOne(DonorProfile::class, 'donor_id', 'user_id');
    }
}
