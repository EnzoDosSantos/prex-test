<?php

namespace App\Http\Middleware;

use Closure;
class EnsureJsonRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!$request->header('Content-Type') || !$request->header('Accept')) {
            return response()->json(['error' => 'Only JSON request are valid.'], 400, ['Content-Type' => 'application/json']);
        }

        if(!$request->isJson()) {
            return response()->json(['error' => 'Only JSON request are valid.'], 400, ['Content-Type' => 'application/json']);
        }

        return $next($request);
    }
}
