<?php

namespace Tests\Feature;

use App\Exceptions\ShortCodeException;
use App\Services\ShortCodeGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortCodeGeneratorTest extends TestCase
{
    use RefreshDatabase;

    private ShortCodeGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generator = new ShortCodeGenerator;
    }

    public function test_creates_link_with_six_char_code(): void
    {
        $link = $this->generator->createLink('https://example.com/page');

        $this->assertSame(ShortCodeGenerator::LENGTH, strlen($link->short_code));
        $this->assertDatabaseHas('links', ['short_code' => $link->short_code]);
    }

    public function test_generated_codes_are_unique_across_many_links(): void
    {
        $codes = collect(range(1, 50))
            ->map(fn () => $this->generator->createLink('https://example.com')->short_code);

        $this->assertSame(50, $codes->unique()->count());
    }

    public function test_accepts_valid_custom_code(): void
    {
        $link = $this->generator->createLink('https://example.com', customCode: 'my-link');

        $this->assertSame('my-link', $link->short_code);
    }

    public function test_rejects_taken_custom_code(): void
    {
        $this->generator->createLink('https://example.com', customCode: 'my-link');

        $this->expectException(ShortCodeException::class);
        $this->expectExceptionMessage('already in use');

        $this->generator->createLink('https://other.com', customCode: 'my-link');
    }

    public function test_rejects_reserved_custom_code(): void
    {
        $this->expectException(ShortCodeException::class);
        $this->expectExceptionMessage('reserved');

        $this->generator->createLink('https://example.com', customCode: 'Admin');
    }

    public function test_rejects_malformed_custom_code(): void
    {
        $this->expectException(ShortCodeException::class);
        $this->expectExceptionMessage('alphanumeric');

        $this->generator->createLink('https://example.com', customCode: 'a!');
    }

    public function test_reserved_words_are_case_insensitive(): void
    {
        $this->assertTrue($this->generator->isReserved('API'));
        $this->assertFalse($this->generator->isReserved('abc123'));
    }
}
