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

    public function searchExternalGifs(Request $request): JsonResponse
    {
        try {
            new RequestValidator($request, 'searchExternalGifs');

            $params = $request->only(['query', 'limit', 'offset']);

            $response = $this->userService->searchGifs('EXTERNAL', $params);

            return response()->json(['gifs' => $response], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function searchExternalGif(Request $request): JsonResponse
    {
        try {
            new RequestValidator($request, 'searchExternalGif');

            $id = $request->input('id');

            $response = $this->userService->searchGif('EXTERNAL', $id);

            return response()->json(['gif' => $response], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function searchInternalGifs(Request $request): JsonResponse
    {
        try {
            new RequestValidator($request, 'searchInternalGifs');

            $params = $request->only(['query', 'limit', 'offset']);

            $response = $this->userService->searchGifs('INTERNAL', $params);

            return response()->json(['gifs' => $response], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function searchInternalGif(Request $request, int $id): JsonResponse
    {
        try {
            $response = $this->userService->searchGif('INTERNAL', $id);

            return response()->json(['gif' => $response], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function updateFavouriteGif(Request $request): JsonResponse
    {
        try {
            new RequestValidator($request, 'setFavouriteGif');

            $gifId = $request->input('id');
            $gifAlias = $request->input('alias');
            $userId = $request->input('user_id');

            $user = Auth::user();

            if($user->id !== $userId){
                throw new Exception('Unauthorized or expired token.', 401);
            }

            $response = $this->userService->updateOrDeleteGif($userId, $gifId, $gifAlias);

            return response()->json(['message' => $response->message], $response->code, [], JSON_UNESCAPED_SLASHES);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }
}
