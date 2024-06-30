<?php

declare(strict_types=1);

namespace Unvurn\Reauth\Auth\Token;

use Illuminate\Http\Request;

class CallbackTokenProvider implements TokenProviderInterface
{
    public function __construct(private mixed $callback)
    {
    }

    public function provideToken(Request $request): ?string
    {
        if (!is_callable($this->callback)) {
            return null;
        }

        return call_user_func($this->callback, $request);
    }
}
