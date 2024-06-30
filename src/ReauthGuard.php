<?php

declare(strict_types=1);

namespace Unvurn\Reauth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Unvurn\Reauth\Auth\Token\TokenDecoderInterface;
use Unvurn\Reauth\Auth\Token\TokenProviderInterface;

class ReauthGuard extends BaseGuard
{
    public function __construct(
        UserProvider           $provider,
        Request                $request,
        /** @var TokenProviderInterface[] $tokenParsers */
        private readonly array $tokenProviders,
        /** @var TokenDecoderInterface[] $tokenParsers */
        private readonly array $tokenParsers)
    {
        parent::__construct($provider, $request);

        $this->outputName = 'token';
    }

    protected function provideUser(): ?Authenticatable
    {
        $user = null;

        //$token = $this->request->bearerToken();
        foreach ($this->tokenProviders as $tokenProvider) {
            $token = $tokenProvider->provideToken($this->request);
            if (!empty($token)) {
                foreach ($this->tokenParsers as $tokenParser) {
                    $credentials = $tokenParser->credentialsFromToken($token, $parsedToken);
                    if (!empty($credentials)) {
                        $user = $this->provider->retrieveByCredentials($credentials);
                        if (!is_null($parsedToken)) {
                            $this->request->attributes->add([$this->outputName => $parsedToken]);
                        }
                        break;
                    }
                }
                break;
            }
        }

        return $user;
    }

    private readonly string $outputName;
}
