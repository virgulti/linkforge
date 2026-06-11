<?php

namespace App\Exceptions;

use Exception;

class ShortCodeException extends Exception
{
    public static function reserved(string $code): self
    {
        return new self("The code '{$code}' is reserved and cannot be used.");
    }

    public static function taken(string $code): self
    {
        return new self("The code '{$code}' is already in use.");
    }

    public static function invalidFormat(string $code): self
    {
        return new self("The code '{$code}' must be 3-16 alphanumeric characters.");
    }

    public static function exhausted(): self
    {
        return new self('Unable to generate a unique short code after maximum attempts.');
    }
}
