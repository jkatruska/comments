<?php

declare(strict_types=1);

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
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new RequestParseException();
        }
        foreach ($data as $key => $value) {
            $request->request->set($key, $value);
        }
    }
}
