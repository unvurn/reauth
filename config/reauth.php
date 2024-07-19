<?php

declare(strict_types=1);

use Unvurn\Reauth\Auth\Token\OpaqueAccessTokenResolver;
use Unvurn\Reauth\Providers\AttributionalUserProvider;

return [
    'expiration' => null,

    'token_prefix' => env('REAUTH_TOKEN_PREFIX', ''),
/*
    'tokens' => [
        'opaque' => [
            'resolver' => OpaqueAccessTokenResolver::class,
            'users' => AttributionalUserProvider::class,
        ],
    ]*/
];
