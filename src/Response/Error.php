<?php

namespace App\Response;

class Error
{
    private string $message;

    /**
     * @param string $message
     */
    private function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * @param string $message
     * @return static
     */
    public static function new(string $message): self
    {
        return new self($message);
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}