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
            'original_url' => 'https://example.com',
            'user_id' => $user->id,
        ]);
        
        $response = $this->actingAs($user)
            ->withSession(['recent_links' => [$link->id]])
            ->get('/');
            
        $response->assertStatus(200);
        $response->assertSee('https://example.com');
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
}
