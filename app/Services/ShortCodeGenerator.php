<?php

namespace App\Services;

use App\Exceptions\ShortCodeException;
use App\Models\Link;
use DateTimeInterface;
use Illuminate\Database\UniqueConstraintViolationException;

class ShortCodeGenerator
{
    private const ALPHABET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    public const LENGTH = 6;

    private const MAX_ATTEMPTS = 10;

    /**
     * Codes that clash with application routes or look official.
     */
    public const RESERVED = [
        'admin', 'api', 'app', 'dashboard', 'docs', 'health',
        'links', 'login', 'logout', 'register', 'status', 'www',
    ];

    /**
     * Create a link with a unique short code, safe under concurrency:
     * uniqueness is enforced by the database unique index, and generated
     * codes are retried when an insert loses the race.
     *
     * @throws ShortCodeException
     */
    public function createLink(
        string $originalUrl,
        ?int $userId = null,
        ?string $customCode = null,
        ?DateTimeInterface $expiresAt = null,
    ): Link {
        if ($customCode !== null) {
            $this->assertValidCustomCode($customCode);

            try {
                return Link::create([
                    'short_code' => $customCode,
                    'original_url' => $originalUrl,
                    'user_id' => $userId,
                    'expires_at' => $expiresAt,
                ]);
            } catch (UniqueConstraintViolationException) {
                throw ShortCodeException::taken($customCode);
            }
        }

        for ($attempt = 0; $attempt < self::MAX_ATTEMPTS; $attempt++) {
            $code = $this->randomCode();

            if ($this->isReserved($code)) {
                continue;
            }

            try {
                return Link::create([
                    'short_code' => $code,
                    'original_url' => $originalUrl,
                    'user_id' => $userId,
                    'expires_at' => $expiresAt,
                ]);
            } catch (UniqueConstraintViolationException) {
                continue;
            }
        }

        throw ShortCodeException::exhausted();
    }

    public function isReserved(string $code): bool
    {
        return in_array(strtolower($code), self::RESERVED, true);
    }

    /**
     * @throws ShortCodeException
     */
    private function assertValidCustomCode(string $code): void
    {
        if (preg_match('/^[0-9a-zA-Z\-_]{3,16}$/', $code) !== 1) {
            throw ShortCodeException::invalidFormat($code);
        }

        if ($this->isReserved($code)) {
            throw ShortCodeException::reserved($code);
        }
    }

    private function randomCode(): string
    {
        $max = strlen(self::ALPHABET) - 1;
        $code = '';

        for ($i = 0; $i < self::LENGTH; $i++) {
            $code .= self::ALPHABET[random_int(0, $max)];
        }

        return $code;
    }
}
