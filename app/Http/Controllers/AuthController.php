<?php

namespace App\Http\Controllers;

use App\Http\Utils\RequestValidator;
use Exception;
use Illuminate\Http\Request;
use App\Http\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function createSession(Request $request): JsonResponse
    {
        try {
            new RequestValidator($request, 'createSession');

            $credentials = $request->only(['email', 'password']);

            $token = $this->authService->createUserSession('email_password', $credentials);

            return response()->json(['token' => $token], 200);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }
}
