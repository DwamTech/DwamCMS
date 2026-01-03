<?php

namespace Database\Seeders;

use App\Models\SupportSetting;
use Illuminate\Database\Seeder;

class ModuleSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'module_articles_enabled' => 'true',
            'module_audios_enabled' => 'true',
            'module_visuals_enabled' => 'true',
            'module_galleries_enabled' => 'true',
            'module_library_enabled' => 'true',
            'module_links_enabled' => 'true',
        ];

        foreach ($settings as $key => $value) {
            SupportSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
