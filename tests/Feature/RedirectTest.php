<?php

namespace Tests\Feature;

use App\Jobs\RecordClick;
use App\Models\Link;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirects_to_original_url_and_queues_click_tracking(): void
    {
        Queue::fake();

        $link = Link::factory()->create([
            'short_code' => 'abc123',
            'original_url' => 'https://example.com/landing',
            'expires_at' => null,
        ]);

        $response = $this->get('/abc123', [
            'Referer' => 'https://google.com',
            'User-Agent' => 'PHPUnit',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('https://example.com/landing');

        Queue::assertPushed(RecordClick::class, function (RecordClick $job) use ($link) {
            return $job->linkId === $link->id
                && $job->referrer === 'https://google.com'
                && $job->userAgent === 'PHPUnit';
        });
    }

    public function test_expired_link_returns_404(): void
    {
        Link::factory()->create([
            'short_code' => 'old123',
            'expires_at' => now()->subDay(),
        ]);

        $this->get('/old123')->assertNotFound();
    }

    public function test_unknown_code_returns_404(): void
    {
        $this->get('/zzzzzz')->assertNotFound();
    }

    public function test_record_click_job_persists_click(): void
    {
        $link = Link::factory()->create(['expires_at' => null]);

        (new RecordClick($link->id, now()->toDateTimeString(), 'https://ref.example', 'UA', 'deadbeef'))->handle();

        $this->assertDatabaseHas('clicks', [
            'link_id' => $link->id,
            'referrer' => 'https://ref.example',
        ]);
    }
}
