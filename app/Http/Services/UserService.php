<?php

namespace App\Http\Services;

use Exception;
use App\Models\Gifs;
use App\Http\Helpers\CacheClient;
use App\Http\Helpers\HttpClient;
use App\Http\Helpers\ResponseFormater;
use App\Models\UserGifs;

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

    public function searchGifs(string $driver, array $params): array
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
            $gifs = $this->searchExternalGifs($search, $limit, $offset);
        } else {
            $gifs = $this->searchInternalGifs($search, $limit, $offset);
        }

        $this->cache->set($cacheKey, $gifs, 120);

        return $gifs;
    }

    public function searchGif(string $driver, string | int $identifier): array
    {
        $cacheKey = $identifier . '_driver_' . $driver;

        $cached = $this->cache->get($cacheKey);

        if(isset($cached)){
            return $cached;
        }

        if($driver === 'EXTERNAL'){
            $gif = $this->searchExternalGif($identifier);
        } else {
            $gif = $this->searchInternalGif($identifier);
        }

        $this->cache->set($cacheKey, $gif, 120);

        return $gif;
    }

    public function updateOrDeleteGif(int $userId, int $gifId, string $gifAlias): object
    {
        $gif = UserGifs::where('user_id', $userId)
                        ->where('gif_id', $gifId)
                        ->first();

        if(isset($gif)){
            $gif->delete();

            $message = 'Gif has been removed from favorites.';
            $code = 200;
        } else {
            UserGifs::create([
                'user_id' => $userId,
                'gif_id' => $gifId,
                'alias' => $gifAlias
            ]);

            $message = 'The gif has been saved in favorites.';
            $code = 201;
        }

        return (object) ['message' => $message, 'code' => $code];
    }

    private function searchExternalGif(string | int $identifier): array
    {
        $token = 'AhYIFREbw68cipBiUT9YxHAmBhCWu8mz';

        $endpoint = "https://api.giphy.com/v1/gifs/$identifier?api_key=$token";

        $response = $this->httpClient->get($endpoint);

        $this->validateGifResponse($identifier, $response);

        $response->data['data'] = [$response->data['data']];

        $path = 'data';
        $validFields = ['id' => 'external_id', 'embed_url' => 'url', 'title' => 'title'];

        $output = $this->formater::format($response->data, $path, $validFields);

        Gifs::insert($output);

        return $output[0];
    }

    private function searchInternalGif(string | int $identifier): array
    {

        if(is_numeric($identifier)){
            $gif = Gifs::find($identifier);
        } else {
            $gif = Gifs::where('external_id', $identifier)
                        ->first();
        }


        if(!isset($gif)){
            throw new Exception("No results available on search: $identifier" , 404);
        }

        return $gif->toArray();
    }

    private function searchExternalGifs(string $search, string|int $limit, string|int $offset): array
    {
        $token = 'AhYIFREbw68cipBiUT9YxHAmBhCWu8mz';

        $endpoint = "https://api.giphy.com/v1/gifs/search?api_key=$token&q=$search&limit=$limit&offset=$offset";

        $response = $this->httpClient->get($endpoint);

        $this->validateGifResponse($search, $response);

        $path = 'data';
        $validFields = ['id' => 'external_id', 'embed_url' => 'url', 'title' => 'title'];

        $gifs = $this->formater::format($response->data, $path, $validFields);

        Gifs::insert($gifs);

        return $gifs;
    }

    private function searchInternalGifs(string $search, string|int $limit, string|int $offset): array
    {
        $gifs = Gifs::where('title', 'like', '%' . $search . '%')
                    ->limit($limit)
                    ->offset($offset)
                    ->get()
                    ->toArray();

        if(sizeof($gifs) === 0){
            throw new Exception("No results available on search: $search" , 404);
        }

        return $gifs;
    }

    private function validateGifResponse(string $identifier, object $response): void
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
