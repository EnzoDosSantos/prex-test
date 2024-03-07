<?php

namespace App\Http\Services;

use Exception;
use App\Http\Helpers\CacheClient;
use App\Http\Helpers\HttpClient;
use App\Http\Helpers\ResponseFormater;
use Illuminate\Support\Facades\Log;

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

    public function searchGifts(array $params): array
    {
        $search = $params['query'];
        $limit = $params['limit'] ?? 2;
        $offset = $params['offset'] ?? 0;

        $identifier = $search . '_limit_' . $limit . '_offset_' . $offset;

        $cached = $this->cache->get($identifier);

        if(isset($cached)){
            return $cached;
        }

        $token = 'AhYIFREbw68cipBiUT9YxHAmBhCWu8mz';

        $endpoint = "https://api.giphy.com/v1/gifs/search?api_key=$token&q=$search&limit=$limit&offset=$offset";

        $response = $this->httpClient->get($endpoint);

        if($response->error){
            throw new Exception('Error fetching data: ' . $response->msg, $response->status);
        }

        if(sizeof($response->data['data']) === 0){
            throw new Exception('No results available on search: ' . $search , 404);
        }

        $path = 'data';
        $validFields = ['id' => 'external_id', 'embed_url' => 'url', 'title' => 'name'];

        $output = $this->formater::format($response->data, $path, $validFields);

        $this->cache->set($identifier, $output, 120);

        return $output;
    }

    public function searchGift(string $identifier): array
    {
        $cached = $this->cache->get($identifier);

        if(isset($cached)){
            return $cached;
        }

        $token = 'AhYIFREbw68cipBiUT9YxHAmBhCWu8mz';

        $endpoint = "https://api.giphy.com/v1/gifs/$identifier?api_key=$token";

        $response = $this->httpClient->get($endpoint);

        if($response->error){
            throw new Exception("Error fetching data on search: $identifier. Message: $response->msg", $response->status);
        }

        if(sizeof($response->data['data']) === 0){
            throw new Exception("No results available on search: $identifier" , 404);
        }

        $response->data['data'] = [$response->data['data']];

        $path = 'data';
        $validFields = ['id' => 'external_id', 'embed_url' => 'url', 'title' => 'name'];

        $output = $this->formater::format($response->data, $path, $validFields);

        $this->cache->set($identifier, $output, 120);

        return $output;
    }
}
