<?php

declare(strict_types=1);

namespace Unvurn\Reauth\Auth\Token;

class NullTokenDecoder implements TokenDecoderInterface
{
    public function __construct(
        private readonly string $key
    )
    {
    }

    public function credentialsFromToken(string $tokenString, mixed &$outputToken = null): array
    {
        $outputToken = $tokenString;
        return [ $this->key => $tokenString ];
    }
}
