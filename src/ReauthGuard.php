<?php

declare(strict_types=1);

namespace Unvurn\Reauth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Unvurn\Reauth\Auth\Token\TokenResolverInterface;


class ReauthGuard extends BaseGuard
{
    public function __construct(
        UserProvider           $provider,
        Request                $request,
        /** @var GuardPipeline[] $pipelines */
        private readonly array $pipelines)
    {
        parent::__construct($provider, $request);

        $this->outputName = 'token';
    }

    protected function provideUser(): ?Authenticatable
    {
        $user = null;

        foreach ($this->pipelines as $spec) {
            $token = $spec->tokenProvider->provideToken($this->request);
            if (!empty($token)) {
                $decodedToken = null;
                if ($spec->tokenResolver instanceof TokenResolverInterface) {
                    $credentials = $spec->tokenResolver->credentialsFromToken($token, $decodedToken);
                } else {
                    $credentials = call_user_func_array($spec->tokenResolver, [$token, $decodedToken]);
                }
                if (!empty($credentials)) {
                    $user = $spec->userProvider->retrieveByCredentials($credentials);
                    if (!is_null($decodedToken)) {
                        $this->request->attributes->add([$this->outputName => $decodedToken]);
                    }
                }
            }
            break;
        }

        return $user;
    }

    private readonly string $outputName;
}
