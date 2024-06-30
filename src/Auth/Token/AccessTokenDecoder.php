<?php

declare(strict_types=1);

namespace Unvurn\Reauth\Auth\Token;

use Unvurn\Reauth\HashedToken;

class AccessTokenDecoder implements TokenDecoderInterface
{
    public function credentialsFromToken(string $tokenString, mixed &$outputToken = null): array
    {
        $comps = explode('|', $tokenString, 2);
        $outputToken = new HashedToken(end($comps));

        return [
            'id' => count($comps) > 1 ? $comps[0] : null,
            'access_token' => [
                'token' => $outputToken
            ]
        ];
    }
}
