<?php

declare(strict_types=1);

namespace Unvurn\Reauth\Providers;

use Illuminate\Contracts\Auth\UserProvider;
use Unvurn\Reauth\Models\UserOpenIdConnection;

class OpenIdUserProvider extends AttributionalUserProvider implements UserProvider
{
    public function __construct(UserProvider $userProvider)
    {
        parent::__construct(
            $userProvider,
            UserOpenIdConnection::class,
            'openid');
    }
}
