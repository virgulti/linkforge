<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads_successfully(): void
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('Shorten a New Link');
    }

    public function test_user_can_shorten_link_via_web_form(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->post('/', [
                'url' => 'https://example.com',
            ]);
            
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('links', [
            'original_url' => 'https://example.com',
            'user_id' => $user->id,
        ]);
        
        $link = Link::where('original_url', 'https://example.com')->first();
        $this->assertTrue(session()->has('recent_links'));
        $this->assertContains($link->id, session()->get('recent_links'));
    }

    public function test_shortened_links_in_session_are_displayed_on_homepage(): void
    {
        $user = User::factory()->create();
        $link = Link::factory()->create([
            'original_url' => 'https://unique-test-url-value-should-be-asserted.com',
            'user_id' => $user->id,
        ]);
        
        $response = $this->actingAs($user)
            ->withSession(['recent_links' => [$link->id]])
            ->get('/');
            
        $response->assertStatus(200);
        $response->assertSee('https://unique-test-url-value-should-be-asserted.com');
        $response->assertSee($link->short_code);
    }

    public function test_validation_errors_are_displayed(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->post('/', [
                'url' => 'invalid-url',
            ]);
            
        $response->assertRedirect();
        $response->assertSessionHasErrors('url');
        
        $this->assertDatabaseMissing('links', [
            'original_url' => 'invalid-url',
        ]);
    }

    public function test_custom_code_clash_returns_error(): void
    {
        $user = User::factory()->create();
        $existingLink = Link::factory()->create([
            'short_code' => 'taken',
        ]);
        
        $response = $this->actingAs($user)
            ->post('/', [
                'url' => 'https://example.com',
                'custom_code' => 'taken',
            ]);
            
        $response->assertRedirect();
        $response->assertSessionHasErrors('custom_code');
        
        $this->assertDatabaseMissing('links', [
            'original_url' => 'https://example.com',
            'short_code' => 'taken',
        ]);
    }

    public function test_analytics_page_loads_successfully(): void
    {
        $user = User::factory()->create();
        $link = Link::factory()->create([
            'original_url' => 'https://example-to-test-analytics.com',
            'user_id' => $user->id,
            'short_code' => 'testana',
        ]);
        
        $link->clicks()->create([
            'clicked_at' => now(),
            'referrer' => 'https://github.com',
            'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1',
            'ip_hash' => 'dummyhash',
        ]);

        $response = $this->get('/links/testana/analytics');
        
        $response->assertStatus(200);
        $response->assertSee('https://example-to-test-analytics.com');
        $response->assertSee('testana');
        $response->assertSee('1');
        $response->assertSee('https://github.com');
    }

    public function test_analytics_page_returns_404_for_unknown_code(): void
    {
        $response = $this->get('/links/nonexistentcode/analytics');
        
        $response->assertStatus(404);
    }
}
