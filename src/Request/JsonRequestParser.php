<?php

namespace App\Request;

use App\Exception\RequestParseException;
use Symfony\Component\HttpFoundation\Request;

class JsonRequestParser implements RequestParserInterface
{
    /**
     * @param Request $request
     * @throws RequestParseException
     */
    public static function parse(Request $request): void
    {
        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RequestParseException();
        }
        foreach ($data as $key => $value) {
            $request->request->set($key, $value);
        }
    }
}
