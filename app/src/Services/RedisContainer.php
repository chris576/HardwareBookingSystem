<?php

namespace App\Service;

use Symfony\Component\Cache\Adapter\RedisAdapter;

class RedisContainer
{

    private $redisClient;

    public function __construct(string $redis_dsn)
    {
        $this->redisClient = RedisAdapter::createConnection($redis_dsn);
    }

    /**
     * Returns the object, or null if nothing was found.
     */
    public function get($key): mixed
    {
        return $this->redisClient->__call("GET", ["key" => $key]);
    }

    public function delete($key): mixed
    {
        return $this->redisClient->__call("DELETE", ["key" => $key]);
    }

    public function set($key, $value): mixed
    {
        return $this->redisClient->__call("SET", ["key" => $key, "value" => $value]);
    }
}