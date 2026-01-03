<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasDefaultSection;


class Visual extends Model
{
    use HasFactory, HasDefaultSection;


    protected $fillable = [
        'section_id',
        'user_id',
        'title',
        'description',
        'type', // 'upload', 'link'
        'file_path',
        'url',
        'thumbnail',
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

    // Accessor for thumbnail full URL
    public function getThumbnailAttribute($value)
    {
        if ($value) {
            return asset('storage/'.$value);
        }

        return null;
    }

    // Accessor for file_path full URL
    public function getFilePathAttribute($value)
    {
        if ($value) {
            return asset('storage/'.$value);
        }

        return null;
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function user() // Author
    {
        return $this->belongsTo(User::class);
    }
}
