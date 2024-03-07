<?php

namespace App\Http\Controllers;

use Exception;
use App\Http\Utils\RequestValidator;
use Illuminate\Http\Request;
use App\Http\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function searchExternalGifts(Request $request): JsonResponse
    {
        try {
            new RequestValidator($request, 'searchExternalGifts');

            $params = $request->only(['query', 'limit', 'offset']);

            $response = $this->userService->searchGifts('EXTERNAL', $params);

            return response()->json(['gifts' => $response], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function searchExternalGift(Request $request): JsonResponse
    {
        try {
            new RequestValidator($request, 'searchExternalGift');

            $id = $request->input('id');

            $response = $this->userService->searchGift('EXTERNAL', $id);

            return response()->json(['gift' => $response], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function searchInternalGifts(Request $request): JsonResponse
    {
        try {
            new RequestValidator($request, 'searchInternalGifts');

            $params = $request->only(['query', 'limit', 'offset']);

            $response = $this->userService->searchGifts('INTERNAL', $params);

            return response()->json(['gifts' => $response], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function searchInternalGift(Request $request, int $id): JsonResponse
    {
        try {
            $response = $this->userService->searchGift('INTERNAL', $id);

            return response()->json(['gift' => $response], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function updateFavouriteGift(Request $request): JsonResponse
    {
        try {
            new RequestValidator($request, 'setFavouriteGift');

            $giftId = $request->input('id');
            $giftAlias = $request->input('alias');
            $userId = $request->input('user_id');

            $user = Auth::user();

            if($user->id !== $userId){
                throw new Exception('Unauthorized or expired token.', 401);
            }

            $response = $this->userService->updateOrDeleteGift($userId, $giftId, $giftAlias);

            return response()->json(['message' => $response->message], $response->code, [], JSON_UNESCAPED_SLASHES);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }
}
