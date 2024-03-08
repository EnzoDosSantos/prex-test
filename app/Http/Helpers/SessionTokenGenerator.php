<?php

namespace App\Http\Helpers;

use App\Models\User;

interface ITokenGenerator
{
    public function createSessionToken(User $user): string;
}

class SessionTokenGenerator implements ITokenGenerator
{
    public function createSessionToken(User $user): string
    {
        $token = $user->createToken('auth_token')->plainTextToken;

        return explode('|', $token)[1];
    }
}
