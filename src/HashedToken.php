<?php

declare(strict_types=1);

namespace Unvurn\Reauth;

readonly class HashedToken
{
    public function __construct(
        public string $token,
        public string $algo = 'sha256'
    )
    {
    }

    public function __toString(): string
    {
        return hash($this->algo, $this->token);
    }
}
