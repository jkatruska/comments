<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;
use Throwable;

class RequestParseException extends Exception
{
    public function __construct($message = 'Cannot parse request body', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
