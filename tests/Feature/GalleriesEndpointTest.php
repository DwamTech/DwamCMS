<?php

namespace Tests\Feature;

use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Models\Section;
use App\Models\User;
use Tests\Concerns\RefreshDatabaseWithForce;
use Tests\TestCase;

class GalleriesEndpointTest extends TestCase
{
    use RefreshDatabaseWithForce;

    public function test_galleries_index_returns_created_gallery(): void
    {
        $user = User::factory()->create();

        $section = Section::create([
            'name' => 'General',
            'slug' => 'general',
        ]);

        $gallery = Gallery::create([
            'section_id' => $section->id,
            'user_id' => $user->id,
            'name' => 'Test Gallery',
            'description' => 'Test Description',
            'keywords' => 'a,b,c',
            'rating' => 4,
        ]);

        GalleryImage::create([
            'gallery_id' => $gallery->id,
            'image_path' => 'galleries/images/test.jpg',
            'sort_order' => 1,
        ]);

        $response = $this->getJson('/api/galleries');

        $response->assertOk();
        $this->assertGreaterThanOrEqual(1, $response->json('total'));
        $this->assertNotEmpty($response->json('data'));
    }
}
