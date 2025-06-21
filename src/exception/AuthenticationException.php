<?php

namespace Blerify\Exception;

use Exception;
use Throwable;

class AuthenticationException extends Exception
{
    private array $details;

    public function __construct(string $message = "Authentication failed", int $code = 401, $details = [], Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->details = is_array($details) ? $details : [];
    }

    public function getDetails(): array
    {
        return $this->details;
    }
}
