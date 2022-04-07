<?php

namespace Darkink\AuthorizationServer\Services\_Default;

use Darkink\AuthorizationServer\Services\ICache;
use DateInterval;
use DateTimeInterface;
use Error;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DefaultCache implements ICache
{
    private const KEYSEPARATOR = ':';
    private string | null $_item_type = null;

    public function __construct(string $item_type)
    {
        $this->_item_type = $item_type;
    }

    // public function setType(string $item_type)
    // {
    //     $this->_item_type = $item_type;
    // }

    private function getKey(string $key)
    {
        if ($this->_item_type == null) {
            throw new Error('ItemType cannot be null, please set it using setType');
        }
        return $this->_item_type . self::KEYSEPARATOR . $key;
    }

    public function get(string $key): mixed
    {
        $cache_key = $this->getKey($key);
        $result = Cache::get($cache_key);
        return $result;
    }

    public function getAndSet(string $key, DateTimeInterface|DateInterval|int $duration, callable $callable): mixed
    {
        if ($callable == null) {
            throw new Error('callable cannot be null');
        }
        if ($key == null) {
            return null;
        }

        $item = $this->get($key);

        if ($item == null) {
            Log::notice("Cache miss for {$key}");

            $item = $callable();

            if ($item != null) {
                Log::notice("Setting item in cache for {$key}");
                $this->set($key, $item, $duration);
            }
        } else {
            Log::notice("Cache hit for {$key}");
        }

        return $item;
    }

    public function set(string $key, mixed $item, DateTimeInterface|DateInterval|int $expiration)
    {
        $cache_key = $this->getKey($key);
        Cache::put($cache_key, $item, $expiration);
    }
}
