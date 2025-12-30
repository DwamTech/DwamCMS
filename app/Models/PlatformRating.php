<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformRating extends Model
{
    use HasFactory;

    protected $fillable = ['rating', 'ip_address', 'user_agent'];

    protected $casts = [
        'rating' => 'integer',
    ];
}
