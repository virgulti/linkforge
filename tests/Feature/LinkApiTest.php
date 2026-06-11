<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LinkApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['shortener.resolve_dns' => false]);
    }

    public function test_guests_cannot_access_the_api(): void
    {
        $this->getJson('/api/links')->assertUnauthorized();
        $this->postJson('/api/links', ['url' => 'https://example.com'])->assertUnauthorized();
    }

    public function test_user_can_create_a_link(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/links', ['url' => 'https://example.com/page']);

        $response->assertCreated();
        $response->assertJsonPath('data.original_url', 'https://example.com/page');
        $this->assertNotEmpty($response->json('data.short_code'));
        $this->assertDatabaseCount('links', 1);
    }

    public function test_user_can_create_a_link_with_custom_code(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/links', ['url' => 'https://example.com', 'custom_code' => 'promo-2026'])
            ->assertCreated()
            ->assertJsonPath('data.short_code', 'promo-2026');
    }

    public function test_unsafe_urls_are_rejected(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/links', ['url' => 'javascript:alert(1)'])->assertUnprocessable();
        $this->postJson('/api/links', ['url' => 'http://127.0.0.1/admin'])->assertUnprocessable();
    }

    public function test_reserved_custom_code_is_rejected(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/links', ['url' => 'https://example.com', 'custom_code' => 'admin'])
            ->assertUnprocessable();
    }

    public function test_index_returns_only_own_links(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Link::factory()->count(2)->create(['user_id' => $user->id]);
        Link::factory()->create(['user_id' => $other->id]);

        Sanctum::actingAs($user);

        $this->getJson('/api/links')->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_cannot_view_or_delete_others_links(): void
    {
        $other = User::factory()->create();
        $foreign = Link::factory()->create(['user_id' => $other->id]);

        Sanctum::actingAs(User::factory()->create());

        $this->getJson("/api/links/{$foreign->id}")->assertNotFound();
        $this->deleteJson("/api/links/{$foreign->id}")->assertNotFound();
        $this->assertDatabaseHas('links', ['id' => $foreign->id]);
    }

    public function test_user_can_delete_own_link(): void
    {
        $user = User::factory()->create();
        $link = Link::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        $this->deleteJson("/api/links/{$link->id}")->assertNoContent();
        $this->assertDatabaseMissing('links', ['id' => $link->id]);
    }
}
