<?php

declare(strict_types=1);

namespace Unvurn\Reauth\Providers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Unvurn\Reauth\Reauth;
use Unvurn\Reauth\HasAccessTokens;
use Unvurn\Reauth\Models\AccessToken;
use Unvurn\Reauth\Models\UserAttributeInterface;

class AccessTokenUserProvider extends AttributionalUserProvider implements UserProvider
{
    public function __construct(
        UserProvider $baseProvider,
        private readonly ?int $expiration = null)
    {
        parent::__construct(
            $baseProvider,
            Reauth::accessTokenModel(),
            'access_token');
    }

    protected function findUserFromAttributes(?UserAttributeInterface $attrs): ?Authenticatable
    {
        if (!($attrs instanceof AccessToken) || !$attrs->isAvailable($this->expiration)) {
            return null;
        }

        $user = parent::findUserFromAttributes($attrs);
        /** @var HasAccessTokens $user */
        $user = $user->withAccessToken($attrs);

        $attrs->touchAccessed();

        /** @var Authenticatable */
        return $user;
    }
}


