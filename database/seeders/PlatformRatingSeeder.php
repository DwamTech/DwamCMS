<?php

namespace Database\Seeders;

use App\Models\PlatformRating;
use Illuminate\Database\Seeder;

class PlatformRatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        PlatformRating::truncate();

        // Sample ratings data
        $ratings = [
            // 5 stars - 40 ratings
            ...array_fill(0, 40, ['rating' => 5]),
            // 4 stars - 30 ratings
            ...array_fill(0, 30, ['rating' => 4]),
            // 3 stars - 15 ratings
            ...array_fill(0, 15, ['rating' => 3]),
            // 2 stars - 10 ratings
            ...array_fill(0, 10, ['rating' => 2]),
            // 1 star - 5 ratings
            ...array_fill(0, 5, ['rating' => 1]),
        ];

        foreach ($ratings as $index => $rating) {
            PlatformRating::create([
                'rating' => $rating['rating'],
                'ip_address' => '192.168.1.' . ($index + 1),
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
            ]);
        }

        $this->command->info('✅ تم إنشاء ' . count($ratings) . ' تقييم للمنصة');
        $this->command->info('   - متوسط التقييم: ' . round(PlatformRating::avg('rating'), 1) . ' نجوم');
    }
}
