<?php

namespace Database\Seeders;

use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DefaultSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first(); // Assign to the first user (likely admin) or create a system user

        if (!$user) {
            $user = User::create([
                'name' => 'System Admin',
                'email' => 'admin@system.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]);
        }

        Section::firstOrCreate(
            ['slug' => 'general'],
            [
                'name' => 'قسم عام',
                'description' => 'القسم الافتراضي للمحتوى العام',
                'is_active' => true,
                'user_id' => $user->id,
            ]
        );
    } 
}
