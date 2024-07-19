<?php

declare(strict_types=1);

namespace Unvurn\Reauth\Auth\Token;

interface TokenResolverInterface
{
    public function credentialsFromToken(string $tokenString, mixed &$outputToken = null): array;
}
