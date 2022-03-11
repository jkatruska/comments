<?php

declare(strict_types=1);

namespace App\Cache;

use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;

class Cache implements CacheInterface
{
    private FilesystemAdapter $cacheAdapter;

    public function __construct()
    {
        $this->cacheAdapter = new FilesystemAdapter();
    }

    /**
     * @param string $key
     * @throws InvalidArgumentException
     * @return CacheItem
     */
    public function get(string $key): CacheItem
    {
        return $this->cacheAdapter->getItem($key);
    }

    /**
     * @param string $key
     * @param mixed $data
     * @param int $ttl
     * @throws InvalidArgumentException
     */
    public function update(string $key, mixed $data, int $ttl = 3600): void
    {
        $item = $this->cacheAdapter->getItem($key);
        $item->set($data);
        $item->expiresAfter($ttl);
        $this->cacheAdapter->save($item);
    }

    /**
     * @param string $key
     * @throws InvalidArgumentException
     */
    public function delete(string $key): void
    {
        $this->cacheAdapter->deleteItem($key);
    }
}
