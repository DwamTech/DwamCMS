<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Seeder;

class ArticlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereIn('role', ['admin', 'editor', 'author'])->pluck('id');
        $sections = Section::pluck('id');

        if ($sections->isEmpty()) {
            // Create some sections if none exist
            $sections = \App\Models\Section::factory(5)->create()->pluck('id');
        }

        // Create 157 Articles
        Article::factory(157)->make()->each(function ($article) use ($users, $sections) {
            $article->user_id = $users->random();
            $article->section_id = $sections->random();
            $article->save();
        });
    }
}
