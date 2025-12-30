<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_visual_update_accepts_post_method(): void
    {
        $found = collect(app('router')->getRoutes()->getRoutes())
            ->contains(function ($route) {
                return $route->uri() === 'api/visuals/{visual}'
                    && in_array('POST', $route->methods(), true)
                    && str_contains($route->getActionName(), 'VisualController@update');
            });

        $this->assertTrue($found);
    }

    public function test_article_update_accepts_post_method(): void
    {
        $found = collect(app('router')->getRoutes()->getRoutes())
            ->contains(function ($route) {
                return $route->uri() === 'api/articles/{article}'
                    && in_array('POST', $route->methods(), true)
                    && str_contains($route->getActionName(), 'ArticleController@update');
            });

        $this->assertTrue($found);
    }
}
