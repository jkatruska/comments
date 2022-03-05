<?php

namespace App\Exception;

use Exception;
use Throwable;

class RateLimitException extends Exception
{
    public function __construct($message = "Too many request", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}