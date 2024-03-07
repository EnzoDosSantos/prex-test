<?php

namespace App\Http\Helpers;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

interface ICredentialsValidator
{
    public function validateCredentials(array $credentials): ?User;
}

class CredentialsValidatorFactory
{
    public function makeValidator(string $loginMethod): ICredentialsValidator
    {
        switch($loginMethod) {
            case 'email_password':
                return new EmailPasswordValidator();
            case 'hash':
                return new HashValidator();
            default:
                throw new Exception('Invalid login method.', 400);
        }
    }
}

class EmailPasswordValidator implements ICredentialsValidator
{
    public function validateCredentials(array $credentials): ?User
    {
        $user = User::where('email', $credentials['email'])
                    ->first();

        if(!isset($user) || !Hash::check($credentials['password'], $user->password)) {
            throw new Exception('Invalid credentials.', 401);
        }

        return $user;
    }
}

class HashValidator implements ICredentialsValidator
{
    public function validateCredentials(array $credentials): ?User
    {

        throw new Exception('Invalid credentials.', 401);
        // $token = LoginTokens::where('token', $credentials['hash'])
        //     ->where('consumed_at', null)
        //     ->where('type', 'session')
        //     ->orderBy('created_at', 'DESC')
        //     ->first();

        // if (!isset($token) || !$token->isValid()) {
            // throw new Exception('Invalid credentials.', 401);
        // }

        // return $token->user;
    }
}
