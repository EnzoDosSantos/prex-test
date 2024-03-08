<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\LogApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next, string $service)
    {
        $request->merge(['service' => $service]);

        return $next($request);
    }

    public function terminate($request, $response)
    {
        $user = Auth::user();

        if(!isset($user)){
            return;
        }

        $requestHttp = $request->getContent();

        $decodedRequest = json_decode($requestHttp);

        if(!isset($decodedRequest)){
            $requestToStore = json_encode((object) []);
        } else {
            $requestToStore = $requestHttp;
        }

        $responseHttp = $response->getContent();

        $decodedResponse = json_decode($responseHttp);

        if(!isset($decodedResponse)){
            $responseToStore = json_encode((object) []);
        } else {
            $responseToStore = $responseHttp;
        }

        $requestService = $request->service ?? 'UNKNOWN';

        LogApp::create([
            'user_id' => $user->id,
            'service' => $requestService,
            'request' => $requestToStore,
            'response' => $responseToStore,
            'endpoint' => $request->fullUrl(),
            'status' => $response->status(),
            'method' => $request->getMethod(),
            'ip' => $this->realIp(),
        ]);

        return;
    }

    private function realIp()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
                foreach ($matches[0] AS $xip) {
                    if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                        $ip = $xip;
                        break;
                    }
                }
            } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CF_CONNECTING_IP'])) {
                $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
            } elseif (isset($_SERVER['HTTP_X_REAL_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            }

        return $ip;

    }
}
