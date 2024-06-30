<?php

declare(strict_types=1);

namespace Unvurn\Reauth;

use Illuminate\Support\Arr;
use Unvurn\Reauth\Models\AccessToken;
use Unvurn\Reauth\Models\UserOpenIdConnection;

class Reauth
{
    public static function accessTokenModel(): string
    {
        return config('reauth.access_token_model', AccessToken::class);
    }

    public static function openIdConnectionModel(): string
    {
        return config('reauth.open_id_connection_model', UserOpenIdConnection::class);
    }

    public function tokenDecoder(string $key): mixed
    {
        return Arr::get($this->tokenDecoders, $key);
    }

    public function registerTokenDecoder(string $key, mixed $value): void
    {
        $this->tokenDecoders[$key] = $value;
    }

    private array $tokenDecoders = [];
}
