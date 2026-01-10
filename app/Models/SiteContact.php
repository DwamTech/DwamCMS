<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteContact extends Model
{
    protected $fillable = [
        // Social Media
        'youtube',
        'twitter',
        'facebook',
        'snapchat',
        'instagram',
        'tiktok',

        // Phone Numbers
        'support_phone',
        'management_phone',
        'backup_phone',

        // Business Details
        'address',
        'commercial_register',
        'email',
    ];

    /**
     * Get social media links as array.
     */
    public function getSocialLinks(): array
    {
        return [
            'youtube' => $this->youtube,
            'twitter' => $this->twitter,
            'facebook' => $this->facebook,
            'snapchat' => $this->snapchat,
            'instagram' => $this->instagram,
            'tiktok' => $this->tiktok,
        ];
    }

    /**
     * Get phone numbers as array.
     */
    public function getPhones(): array
    {
        return [
            'support_phone' => $this->support_phone,
            'management_phone' => $this->management_phone,
            'backup_phone' => $this->backup_phone,
        ];
    }

    /**
     * Get business details as array.
     */
    public function getBusinessDetails(): array
    {
        return [
            'address' => $this->address,
            'commercial_register' => $this->commercial_register,
            'email' => $this->email,
        ];
    }

    /**
     * Get or create the singleton instance.
     */
    public static function getInstance(): self
    {
        return self::firstOrCreate(['id' => 1]);
    }
}
