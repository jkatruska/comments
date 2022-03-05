<?php

namespace App\Request;

use App\Exception\RequestParseException;
use Symfony\Component\HttpFoundation\Request;

class JsonRequestParser
{
    /**
     * @param Request $request
     * @return array|null
     * @throws RequestParseException
     */
    public static function parse(Request $request): ?array
    {
        if (!str_contains($request->headers->get('Content-Type'), 'application/json')) {
            return null;
        }

        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RequestParseException();
        }
        return $data;
    }
}