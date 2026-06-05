<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'booking_date',
        'time_slot',
        'service',
        'notes',
        'status',
    ];

    protected $casts = [
        'booking_date' => 'date',
    ];

    // Convenience scopes for admin filtering
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeForDate($query, string $date)
    {
        return $query->where('booking_date', $date);
    }

    // Returns booked (non-cancelled) slot strings for a given date
    public static function bookedSlotsForDate(string $date): array
    {
        return self::where('booking_date', $date)
            ->whereIn('status', ['pending', 'approved'])
            ->pluck('time_slot')
            ->toArray();
    }
}

