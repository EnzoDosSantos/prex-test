<?php

namespace App\Http\Helpers;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

interface HttpClientInterface
{
    static function makeHttpRequest(string $method, string $url, array $headers, array $parameters);
}

class HttpClient
{
    private $httpClient;
    private $logger;

    public function __construct(HttpBase $httpClient, HttpLogger $logger)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    public function get(string $url, ?string $token = null, ?array $parameters = null): object
    {
        return $this->sendRequest('GET', $url, $token, $parameters);
    }

    public function post(string $url, ?string $token = null, ?array $parameters = null): object
    {
        return $this->sendRequest('POST', $url, $token, $parameters);
    }

    private function sendRequest(string $method, string $url, ?string $token, ?array $parameters): object
    {
        $responseValues = (object) ['data' => null, 'msg' => null, 'status' => 200, 'error' => false];

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];

        try {
            $response = $this->httpClient::makeHttpRequest($method, $url, $headers, $parameters);

            $statusCode = $response->status();

            if ($response->successful()) {
                $data = $response->json();

                $responseValues->data = $data;
            } else {
                $error = $response->json();

                $responseValues->error = true;
                $responseValues->data = $error;
                $responseValues->status = $statusCode;

                $responseValues->msg = $error['meta']['msg'] ?? 'Error on http request';
            }
        } catch (Exception $e) {
            $error = $e->getMessage();

            $responseValues->error = true;
            $responseValues->data = $error;
            $responseValues->status = $statusCode;

            $responseValues->msg = $error;
        }

        $this->logger->store($url, $parameters, $responseValues->data, $statusCode);

        return $responseValues;
    }
}

class HttpBase implements HttpClientInterface
{
    static function makeHttpRequest(string $method, string $url, ?array $headers, ?array $parameters)
    {
        switch (strtoupper($method)) {
            case 'GET':
                return Http::get($url, $parameters);

            case 'POST':
                return Http::withHeaders($headers)->post($url, $parameters);

            default:
                throw new Exception("Unsupported HTTP method: $method", 400);
        }
    }
}

class HttpLogger
{
    public function store(string $endpoint, ?array $request, array $response, string $statusCode): void
    {
        // LogExternalServices::create([
        //     'user_id' => Auth::user()->id,
        //     'endpoint' => $endpoint,
        //     'request' => isset($request) ? json_encode($request) : null,
        //     'response' => $response,
        //     'status_code' => $statusCode
        // ]);
    }
}
