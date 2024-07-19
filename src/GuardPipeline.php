<?php

namespace Unvurn\Reauth;

use Closure;
use Illuminate\Contracts\Auth\UserProvider;
use Unvurn\Reauth\Auth\Token\TokenResolverInterface;
use Unvurn\Reauth\Auth\Token\TokenProviderInterface;

readonly class GuardPipeline
{
    public function __construct(
        public TokenProviderInterface         $tokenProvider,
        public TokenResolverInterface|Closure $tokenResolver,
        public UserProvider                   $userProvider,
    )
    {
    }
}
