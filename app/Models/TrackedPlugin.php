<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackedPlugin extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'last_seen'      => 'datetime',
        'activated_at'   => 'datetime',
        'deactivated_at' => 'datetime',
    ];

    /**
     * Scope query for active sites (status = active and seen within X days, default 30 days)
     */
    public function scopeActive($query, int $days = 30)
    {
        return $query->where('status', 'active')
                     ->where('last_seen', '>=', now()->subDays($days));
    }

    /**
     * Scope query for inactive or deactivated sites
     */
    public function scopeInactive($query, int $days = 30)
    {
        return $query->where(function ($q) use ($days) {
            $q->where('status', 'deactivated')
              ->orWhere('last_seen', '<', now()->subDays($days));
        });
    }

    /**
     * Scope query specifically for deactivated plugins
     */
    public function scopeDeactivated($query)
    {
        return $query->where('status', 'deactivated');
    }

    /**
     * Check if site/plugin is currently active
     */
    public function getIsActiveAttribute(): bool
    {
        if ($this->status === 'deactivated') {
            return false;
        }

        if (!$this->last_seen) {
            return false;
        }

        return $this->last_seen->greaterThanOrEqualTo(now()->subDays(30));
    }
}
