<?php

namespace Tests\Feature;

use App\Models\Section;
use App\Models\User;
use App\Models\Visual;
use Tests\Concerns\RefreshDatabaseWithForce;
use Tests\TestCase;

class VisualsEndpointTest extends TestCase
{
    use RefreshDatabaseWithForce;

    public function test_visuals_index_returns_created_visuals(): void
    {
        $user = User::factory()->create();

        $section = Section::create([
            'name' => 'General',
            'slug' => 'general',
        ]);

        Visual::create([
            'section_id' => $section->id,
            'user_id' => $user->id,
            'title' => 'Test Visual',
            'type' => 'link',
            'url' => 'https://example.com',
        ]);

        $response = $this->getJson('/api/visuals');

        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'total',
        ]);

        $this->assertGreaterThanOrEqual(1, $response->json('total'));
        $this->assertNotEmpty($response->json('data'));
    }
}
