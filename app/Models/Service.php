<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price'];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function getFormattedPriceAttribute(): string
    {
        return 'CHF ' . number_format((float) $this->price, 0, '.', "'");
    }
}
