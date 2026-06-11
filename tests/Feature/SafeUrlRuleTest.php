<?php

namespace Tests\Feature;

use App\Rules\SafeUrl;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class SafeUrlRuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['shortener.resolve_dns' => false]);
    }

    private function passes(string $url): bool
    {
        return Validator::make(['url' => $url], ['url' => new SafeUrl])->passes();
    }

    public function test_accepts_normal_http_and_https_urls(): void
    {
        $this->assertTrue($this->passes('https://example.com/page?q=1'));
        $this->assertTrue($this->passes('http://sub.example.org'));
    }

    public function test_rejects_non_http_schemes(): void
    {
        $this->assertFalse($this->passes('javascript:alert(1)'));
        $this->assertFalse($this->passes('ftp://example.com/file'));
        $this->assertFalse($this->passes('file:///etc/passwd'));
    }

    public function test_rejects_private_and_reserved_ip_literals(): void
    {
        $this->assertFalse($this->passes('http://127.0.0.1/admin'));
        $this->assertFalse($this->passes('http://192.168.1.1'));
        $this->assertFalse($this->passes('http://10.0.0.5/internal'));
        $this->assertFalse($this->passes('http://169.254.169.254/latest/meta-data'));
        $this->assertFalse($this->passes('http://[::1]/'));
    }

    public function test_accepts_public_ip_literal(): void
    {
        $this->assertTrue($this->passes('http://93.184.216.34/'));
    }

    public function test_rejects_blocked_domains_including_subdomains(): void
    {
        config(['shortener.blocked_domains' => ['evil.test']]);

        $this->assertFalse($this->passes('https://evil.test/x'));
        $this->assertFalse($this->passes('https://login.evil.test/x'));
        $this->assertTrue($this->passes('https://notevil.test/x'));
    }

    public function test_rejects_own_host_to_prevent_redirect_loops(): void
    {
        config(['app.url' => 'https://linkforge.test']);

        $this->assertFalse($this->passes('https://linkforge.test/abc123'));
    }

    public function test_rejects_malformed_urls(): void
    {
        $this->assertFalse($this->passes('not-a-url'));
        $this->assertFalse($this->passes('http://'));
    }
}
