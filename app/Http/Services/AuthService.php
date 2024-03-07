<?php

namespace App\Http\Services;

use App\Http\Helpers\CredentialsValidatorFactory;
use App\Http\Helpers\SessionTokenGenerator;

class AuthService
{
    private $credentialsValidatorFactory;
    private $sessionTokenGenerator;

    public function __construct(CredentialsValidatorFactory $credentialsValidatorFactory, SessionTokenGenerator $sessionTokenGenerator)
    {
        $this->credentialsValidatorFactory = $credentialsValidatorFactory;
        $this->sessionTokenGenerator = $sessionTokenGenerator;
    }

    public function createUserSession(string $method, array $credentials): string
    {
        $validator = $this->credentialsValidatorFactory->makeValidator($method);
        $user = $validator->validateCredentials($credentials);

        return $this->sessionTokenGenerator->createSessionToken($user);
    }
}
