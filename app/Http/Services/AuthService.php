<?php

namespace App\Http\Services;

use Exception;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function validateUserCredentials(array $credentials): string
    {
        $user = User::where('email', $credentials['email'])
                    ->first();

        if(!isset($user) || !Hash::check($credentials['password'], $user->password)){
            throw new Exception('Invalid credentials.', 401);
        }

        return $this->createSessionToken($user);
    }

    private function createSessionToken(User $user): string
    {
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        $authToken = explode('|', $token)[1];

        return $authToken;
    }
}
