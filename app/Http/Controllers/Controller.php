<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function handleException(Exception $exception): JsonResponse
    {
        $code = $exception->getCode();

        if($code === 400){
            $error = json_decode($exception->getMessage());
        } else {

            if($code === 0){
                $code = 500;
            }

            $error = $exception->getMessage();
        }

        return response()->json(['error' => $error], $code, ['Content-Type' => 'application/json']);
    }
}
