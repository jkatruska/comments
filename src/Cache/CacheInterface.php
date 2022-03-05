<?php

namespace App\Cache;

use Symfony\Component\Cache\CacheItem;

interface CacheInterface
{
    public function get(string $key): CacheItem;

    public function update(string $key, mixed $data, int $ttl = 3600): void;

    public function delete(string $key): void;
}