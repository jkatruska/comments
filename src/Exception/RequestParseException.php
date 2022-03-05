<?php

namespace App\Exception;

use Exception;
use Throwable;

class RequestParseException extends Exception
{
    public function __construct($message = "Invalid json provided", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}