<?php

declare(strict_types=1);

use Unvurn\Reauth\Auth\Token\OpaqueAccessTokenResolver;
use Unvurn\Reauth\Providers\AccessTokenUserProvider;

return [
    'guard' => 'bearer',

    'guards' => [
        'bearer' => [
            'driver' => 'bearer',
            'pipelines' => [
                [
                    'resolver' => OpaqueAccessTokenResolver::class,
                    'users' => AccessTokenUserProvider::class
                ]
            ]
        ]
    ]
];
