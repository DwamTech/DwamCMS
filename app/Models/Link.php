<?php

namespace App\Models;

use App\Traits\HasDefaultSection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory, HasDefaultSection;

    protected $fillable = [
        'section_id',
        'user_id',
        'title',
        'description',
        'url',
        'image_path',
        'keywords',
        'views_count',
        'rating',
    ];

    protected $casts = [
        'rating' => 'float',
        'views_count' => 'integer',
        'section_id' => 'integer',
        'user_id' => 'integer',
    ];

    /**
     * Get the full URL for the image path.
     */
    public function getImagePathAttribute($value)
    {
        if ($value) {
            if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
                return $value;
            }

            return asset('storage/' . $value);
        }

        return null;
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
