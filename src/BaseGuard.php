<?php

declare(strict_types=1);

namespace Unvurn\Reauth;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use LogicException;

abstract class BaseGuard implements Guard
{
    use GuardHelpers;

    public function __construct(
        UserProvider   $provider,
        public Request $request)
    {
        $this->provider = $provider;
    }

    public function user(): ?Authenticatable
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        $user = $this->provideUser();

        return $this->user = $user;
    }

    public function validate(array $credentials = []): bool
    {
        throw new LogicException("intentionally undefined function: " . __FUNCTION__);
    }

    public function check(): bool
    {
        return !is_null($this->user());
    }

    public function setRequest(Request $request): static
    {
        $this->request = $request;
        return $this;
    }

    protected abstract function provideUser(): ?Authenticatable;
}
