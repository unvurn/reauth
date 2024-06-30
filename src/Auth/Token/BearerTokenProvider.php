<?php

declare(strict_types=1);

namespace Unvurn\Reauth\Auth\Token;

use Illuminate\Http\Request;

class BearerTokenProvider implements TokenProviderInterface
{
    public function provideToken(Request $request): ?string
    {
        return $request->bearerToken();
    }
}

