<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || strlen($value) > 2048) {
            $fail('validation.url')->translate();

            return;
        }

        $parts = parse_url($value);

        if ($parts === false || empty($parts['host'])) {
            $fail('validation.url')->translate();

            return;
        }

        if (! in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)) {
            $fail('Only http and https URLs can be shortened.');

            return;
        }

        $host = strtolower($parts['host']);

        if ($this->isOwnHost($host)) {
            $fail('URLs pointing to this service cannot be shortened.');

            return;
        }

        if ($this->isBlockedDomain($host)) {
            $fail('This domain cannot be shortened.');

            return;
        }

        if ($this->resolvesToPrivateAddress($host)) {
            $fail('URLs resolving to private or reserved addresses are not allowed.');
        }
    }

    /**
     * Evita redirect loop: niente short link verso il servizio stesso.
     */
    private function isOwnHost(string $host): bool
    {
        $ownHost = strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST));

        return $ownHost !== '' && $host === $ownHost;
    }

    private function isBlockedDomain(string $host): bool
    {
        foreach ((array) config('shortener.blocked_domains', []) as $blocked) {
            $blocked = strtolower(trim((string) $blocked));

            if ($blocked === '') {
                continue;
            }

            if ($host === $blocked || str_ends_with($host, '.'.$blocked)) {
                return true;
            }
        }

        return false;
    }

    private function resolvesToPrivateAddress(string $host): bool
    {
        $literal = trim($host, '[]');

        if (filter_var($literal, FILTER_VALIDATE_IP) !== false) {
            return $this->isPrivateIp($literal);
        }

        if (! config('shortener.resolve_dns', true)) {
            return false;
        }

        $ip = gethostbyname($host);

        // gethostbyname restituisce l'input invariato se la risoluzione fallisce.
        return $ip !== $host && $this->isPrivateIp($ip);
    }

    private function isPrivateIp(string $ip): bool
    {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE,
        ) === false;
    }
}
