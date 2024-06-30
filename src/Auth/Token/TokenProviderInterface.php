<?php

declare(strict_types=1);

namespace Unvurn\Reauth\Auth\Token;

use Illuminate\Http\Request;

interface TokenProviderInterface
{
    public function provideToken(Request $request): ?string;
}
