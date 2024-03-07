<?php

namespace App\Http\Services;

use Exception;
use App\Models\Gifts;
use App\Http\Helpers\CacheClient;
use App\Http\Helpers\HttpClient;
use App\Http\Helpers\ResponseFormater;

class UserService
{

    private $httpClient;
    private $formater;
    private $cache;

    public function __construct(HttpClient $httpClient, ResponseFormater $formater, CacheClient $cache)
    {
        $this->httpClient = $httpClient;
        $this->formater = $formater;
        $this->cache = $cache;
    }

    public function searchGifts(string $driver, array $params): array
    {
        $search = $params['query'];
        $limit = $params['limit'] ?? 10;
        $offset = $params['offset'] ?? 0;

        $cacheKey = $search . '_limit_' . $limit . '_offset_' . $offset . '_driver_' . $driver;

        $cached = $this->cache->get($cacheKey);

        if(isset($cached)){
            return $cached;
        }

        if($driver === 'EXTERNAL'){
            $gifts = $this->searchExternalGifts($search, $limit, $offset);
        } else {
            $gifts = $this->searchInternalGifts($search, $limit, $offset);
        }

        $this->cache->set($cacheKey, $gifts, 120);

        return $gifts;
    }

    public function searchGift(string $driver, string $identifier): array
    {
        $cacheKey = $identifier . '_driver_' . $driver;

        $cached = $this->cache->get($cacheKey);

        if(isset($cached)){
            return $cached;
        }

        if($driver === 'EXTERNAL'){
            $gift = $this->searchExternalGift($identifier);
        } else {
            $gift = $this->searchInternalGift($identifier);
        }

        $this->cache->set($cacheKey, $gift, 120);

        return $gift;
    }

    private function searchExternalGift(string $identifier): array
    {
        $token = 'AhYIFREbw68cipBiUT9YxHAmBhCWu8mz';

        $endpoint = "https://api.giphy.com/v1/gifs/$identifier?api_key=$token";

        $response = $this->httpClient->get($endpoint);

        $this->validateGiftResponse($identifier, $response);

        $response->data['data'] = [$response->data['data']];

        $path = 'data';
        $validFields = ['id' => 'external_id', 'embed_url' => 'url', 'title' => 'title'];

        $output = $this->formater::format($response->data, $path, $validFields);

        Gifts::insert($output);

        return $output[0];
    }

    private function searchInternalGift(int $identifier): array
    {

        if(is_numeric($identifier)){
            $gift = Gifts::find($identifier);
        } else {
            $gift = Gifts::where('external_id', $identifier)
                        ->first();
        }


        if(!isset($gift)){
            throw new Exception("No results available on search: $identifier" , 404);
        }

        return $gift->toArray();
    }

    private function searchExternalGifts(string $search, string|int $limit, string|int $offset): array
    {
        $token = 'AhYIFREbw68cipBiUT9YxHAmBhCWu8mz';

        $endpoint = "https://api.giphy.com/v1/gifs/search?api_key=$token&q=$search&limit=$limit&offset=$offset";

        $response = $this->httpClient->get($endpoint);

        $this->validateGiftResponse($search, $response);

        $path = 'data';
        $validFields = ['id' => 'external_id', 'embed_url' => 'url', 'title' => 'title'];

        $gifts = $this->formater::format($response->data, $path, $validFields);

        Gifts::insert($gifts);

        return $gifts;
    }

    private function searchInternalGifts(string $search, string|int $limit, string|int $offset): array
    {
        $gifts = Gifts::where('title', 'like', '%' . $search . '%')
                    ->limit($limit)
                    ->offset($offset)
                    ->get()
                    ->toArray();

        if(sizeof($gifts) === 0){
            throw new Exception("No results available on search: $search" , 404);
        }

        return $gifts;
    }

    private function validateGiftResponse(string $identifier, object $response): void
    {
        if($response->error){
            throw new Exception("Error fetching data on search: $identifier. Message: $response->msg", $response->status);
        }

        if(sizeof($response->data['data']) === 0){
            throw new Exception("No results available on search: $identifier" , 404);
        }

        return;
    }
}
