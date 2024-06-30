<?php

declare(strict_types=1);

namespace Unvurn\Reauth\Providers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Unvurn\Reauth\Models\UserAttribute;
use Unvurn\Reauth\Models\UserAttributeInterface;

class AttributionalUserProvider implements UserProvider
{
    public function __construct(
        private readonly UserProvider $baseProvider,
        private readonly string       $targetAttributeModel = UserAttribute::class,
        private readonly string       $targetPrefix = 'attributes',
    )
    {
        $this->targetModelInstance = new $this->targetAttributeModel;
    }

    public function retrieveById($identifier): ?Authenticatable
    {
        return $this->baseProvider->retrieveById($identifier);
    }

    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        return $this->baseProvider->retrieveByToken($identifier, $token);
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        return $this->baseProvider->updateRememberToken($user, $token);
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {
        return $this->baseProvider->rehashPasswordIfRequired($user, $credentials, $force);
    }

    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        if (array_key_exists($this->targetPrefix, $credentials)) {
            $query = $this->newQueryWithCredentials($credentials[$this->targetPrefix]);
            $authenticable = $this->findUserFromAttributes($query->first());

            if ($authenticable instanceof Authenticatable) {
                return $authenticable;
            }
        }

        $baseCredentials = array_diff_key($credentials, [$this->targetPrefix => 0]);
        return $this->baseProvider->retrieveByCredentials($baseCredentials);
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        if (array_key_exists($this->targetPrefix, $credentials)) {
            $query = $this->newQueryWithCredentials($credentials[$this->targetPrefix]);
            $auth = $this->findUserFromAttributes($query->first());

            if ($auth instanceof Authenticatable) {
                return $auth->getAuthIdentifier() === $user->getAuthIdentifier();
            }
        }

        $baseCredentials = array_diff_key($credentials, [$this->targetPrefix => 0]);
        return $this->baseProvider->validateCredentials($user, $baseCredentials);
    }

    private function newQueryWithCredentials(array $credentials): Builder
    {
        $q = $this->targetModelInstance->newQuery();
        foreach ($credentials as $k => $v) {
            $q->where($k, $v);
        }
        return $q;
    }

    protected function findUserFromAttributes(?UserAttributeInterface $attrs): ?Authenticatable
    {
        return $attrs?->user;
    }

    private readonly Model $targetModelInstance;
}

