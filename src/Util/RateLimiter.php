<?php

declare(strict_types=1);

namespace App\Util;

use App\Cache\CacheInterface;
use App\Exception\RateLimitException;

class RateLimiter
{
    private CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param string $key
     * @param int $timeout
     */
    public function increment(string $key, int $timeout = 300): void
    {
        $item = $this->cache->get($key);
        $count = $item->isHit() ? (int) $item->get() : 0;
        ++$count;
        $this->cache->update($key, (string) $count, $timeout);
    }

    /**
     * @param string $key
     * @param int $limit
     * @throws RateLimitException
     */
    public function check(string $key, int $limit): void
    {
        $item = $this->cache->get($key);
        $count = $item->isHit() ? (int) $item->get() : 0;
        if ($count >= $limit) {
            throw new RateLimitException();
        }
    }
}
