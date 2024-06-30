<?php

declare(strict_types=1);

namespace Unvurn\Reauth\Auth\Token;

interface TokenDecoderInterface
{
    public function credentialsFromToken(string $tokenString, mixed &$outputToken = null): array;
}
