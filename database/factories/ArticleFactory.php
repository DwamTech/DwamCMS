<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence();

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::random(5),
            'excerpt' => fake()->paragraph(),
            'content' => fake()->paragraphs(3, true),
            'author_name' => fake()->name(),
            'featured_image' => 'placeholders/article.jpg', // Ensure this file exists or use null
            'gregorian_date' => fake()->date(),
            // Simple Hijri conversion approximation or random string
            'hijri_date' => '1445-01-01',
            'references' => fake()->url(),
            'keywords' => implode(',', fake()->words(5)),
            'status' => fake()->randomElement(['published', 'draft', 'archived']),
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'views_count' => fake()->numberBetween(100, 10000),
            // User and Section will be assigned in Seeder
        ];
    }
}
