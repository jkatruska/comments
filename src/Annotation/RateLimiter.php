<?php

declare(strict_types=1);

namespace App\Annotation;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"METHOD"})
 */
final class RateLimiter
{
    /** @Required */
    private string $name;

    /**
     * @Required
     * @Enum({"ip"})
     */
    private string $identifier;
    private int $limit;
    private int $timeout;

    public function __construct(
        string $name,
        string $identifier,
        int $limit = 5,
        int $timeout = 300
    ) {
        $this->name = $name;
        $this->identifier = $identifier;
        $this->limit = $limit;
        $this->timeout = $timeout;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }
}
