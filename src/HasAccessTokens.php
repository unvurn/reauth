<?php

declare(strict_types=1);

namespace Unvurn\Reauth;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use Unvurn\Reauth\Models\AccessToken;

/**
 * @method morphMany(string $accessTokenModel, string $string)
 */
trait HasAccessTokens
{
    public function tokens(): MorphMany
    {
        return $this->morphMany(Reauth::accessTokenModel(), 'user');
    }

    public function tokenCan(string $ability): bool
    {
        return !empty($this->accessToken) && $this->accessToken->can($ability);
    }

    public function createAccessToken(string $name, array $abilities = ['*'], DateTimeInterface $expiresAt = null): string
    {
        $plainTextToken = $this->generateTokenString();

        /** @var AccessToken $token */
        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken),
            'abilities' => $abilities,
            'expires_at' => $expiresAt,
        ]);

        return $token->getKey().'|'.$plainTextToken;
    }

    public function generateTokenString(): string
    {
        return sprintf(
            '%s%s%s',
            config('reauth.token_prefix', ''),
            $tokenEntropy = Str::random(40),
            hash('crc32b', $tokenEntropy)
        );
    }

    public function currentAccessToken(): AccessToken
    {
        return $this->accessToken;
    }

    public function withAccessToken(AccessToken $accessToken): static
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    protected AccessToken $accessToken;
}
