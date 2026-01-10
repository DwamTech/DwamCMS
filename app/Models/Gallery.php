<?php

namespace App\Models;

use App\Traits\HasDefaultSection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasDefaultSection, HasFactory;

    protected $fillable = [
        'section_id',
        'user_id',
        'name',
        'description',
        'cover_image',
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

    public function getCoverImageAttribute($value)
    {
        if ($value) {
            if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
                return $value;
            }

            return asset('storage/'.$value);
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

    public function images()
    {
        return $this->hasMany(GalleryImage::class)->orderBy('sort_order');
    }
}
