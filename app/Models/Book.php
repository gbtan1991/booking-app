<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $table = 'book';

    protected $fillable = [
        'service',
        'date',
        'time',
        'customer_name',
        'customer_email',
        'customer_telephone',
        'customer_notes',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────

    public function scopeCurrent($query)
    {
        return $query->where('status', 'current');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeForDate($query, string $date)
    {
        return $query->where('date', $date);
    }

    // Returns booked (current) time strings for a given date
    public static function bookedTimesForDate(string $date): array
    {
        return self::where('date', $date)
            ->where('status', 'current')
            ->pluck('time')
            ->toArray();
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'current'   => 'Current',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default     => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'current'   => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default     => 'bg-stone-100 text-stone-600',
        };
    }
}
