<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'gallery_id',
        'image_path',
        'sort_order',
    ];

    protected $casts = [
        'gallery_id' => 'integer',
        'sort_order' => 'integer',
    ];

    public function getImagePathAttribute($value)
    {
        if ($value) {
            if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
                return $value;
            }

            return asset('storage/'.$value);
        }

        return null;
    }

    public function gallery()
    {
        return $this->belongsTo(Gallery::class);
    }
}
