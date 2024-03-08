<?php

namespace App\Http\Helpers;

use Exception;
use Illuminate\Support\Facades\Cache;

interface ICache
{
    public function set(string $identifier, mixed $payload, int $ttl): bool;
    public function get(string $identifier): ?array;
}

class CacheClient implements ICache
{

    protected $driver;
    public function __construct()
    {
        $this->driver = 'database';
    }
    public function get(string $identifier): ?array
    {
        try {
            if(Cache::store($this->driver)->has($identifier)){
                return Cache::store($this->driver)->get($identifier);
            }

            return null;
        } catch(Exception $e){
            throw new Exception($e->getMessage(), 500);
        }
    }

    public function set(string $identifier, mixed $payload, int $ttl): bool
    {
        try {
            return Cache::store($this->driver)->put($identifier, $payload, $ttl);
        } catch(Exception $e){
            throw new Exception($e->getMessage(), 500);
        }
    }
}
