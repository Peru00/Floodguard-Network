<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReliefCamp extends Model
{
    protected $primaryKey = 'camp_id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'camp_id',
        'camp_name',
        'location',
        'capacity',
        'current_occupancy',
        'managed_by',
        'last_updated'
    ];

    protected $casts = [
        'last_updated' => 'datetime',
    ];

    /**
     * Get the user who manages this camp
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'managed_by', 'user_id');
    }

    /**
     * Get occupancy percentage
     */
    public function getOccupancyPercentageAttribute(): float
    {
        if ($this->capacity <= 0) {
            return 0;
        }
        return min(100, ($this->current_occupancy / $this->capacity) * 100);
    }

    /**
     * Get occupancy status
     */
    public function getOccupancyStatusAttribute(): string
    {
        $percentage = $this->occupancy_percentage;
        
        if ($percentage >= 100) {
            return 'full';
        } elseif ($percentage >= 80) {
            return 'almost-full';
        } else {
            return 'available';
        }
    }

    /**
     * Get available spaces
     */
    public function getAvailableSpacesAttribute(): int
    {
        return max(0, $this->capacity - $this->current_occupancy);
    }
}
