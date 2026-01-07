<?php

namespace Database\Seeders;

use App\Models\SiteContact;
use Illuminate\Database\Seeder;

class SiteContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SiteContact::updateOrCreate(
            ['id' => 1],
            [
                // Social Media
                'youtube' => null,
                'twitter' => null,
                'facebook' => null,
                'snapchat' => null,
                'instagram' => null,
                'tiktok' => null,

                // Phone Numbers
                'support_phone' => null,
                'management_phone' => null,
                'backup_phone' => null,

                // Business Details
                'address' => null,
                'commercial_register' => null,
                'email' => null,
            ]
        );
    }
}
