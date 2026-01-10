<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'author_name' => fake()->name(),
            'description' => fake()->paragraph(),
            'source_type' => fake()->randomElement(['file', 'link', 'embed']),
            'file_path' => 'placeholders/book.pdf',
            'cover_type' => fake()->randomElement(['auto', 'upload']),
            'cover_path' => 'placeholders/book_cover.jpg',
            'type' => fake()->randomElement(['single', 'part']),
            'views_count' => fake()->numberBetween(0, 10000),
            'rating_sum' => fake()->randomFloat(2, 0, 50),
            'rating_count' => fake()->numberBetween(0, 50),
        ];
    }
}
