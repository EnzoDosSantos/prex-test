<?php

namespace App\Http\Controllers;

use Exception;
use App\Http\Utils\RequestValidator;
use Illuminate\Http\Request;
use App\Http\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function searchGifts(Request $request): JsonResponse
    {
        try {
            new RequestValidator($request, 'searchGifts');

            $params = $request->only(['query', 'limit', 'offset']);

            $response = $this->userService->searchGifts($params);

            return response()->json(['gifts' => $response], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function searchGift(Request $request): JsonResponse
    {
        try {
            new RequestValidator($request, 'searchGift');

            $id = $request->input('id');

            $response = $this->userService->searchGift($id);

            return response()->json(['gift' => $response], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }
}
