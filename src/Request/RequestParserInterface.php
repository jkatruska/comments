<?php

declare(strict_types=1);

namespace App\Request;

use Symfony\Component\HttpFoundation\Request;

interface RequestParserInterface
{
    /**
     * @param Request $request
     */
    public static function parse(Request $request): void;
}
