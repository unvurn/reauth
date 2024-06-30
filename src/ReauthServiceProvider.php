<?php

declare(strict_types=1);

namespace Unvurn\Reauth;

use Illuminate\Contracts\Auth\Factory;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Unvurn\Reauth\Auth\Token\AccessTokenDecoder;
use Unvurn\Reauth\Auth\Token\BearerTokenProvider;
use Unvurn\Reauth\Auth\Token\NullTokenDecoder;
use Unvurn\Reauth\Auth\Token\JsonAndKeyPathTokenProvider;
use Unvurn\Reauth\Auth\Token\TokenDecoderInterface;
use Unvurn\Reauth\Auth\Token\TokenProviderInterface;
use Unvurn\Reauth\Providers\AccessTokenUserProvider;
use Unvurn\Reauth\Providers\AttributionalUserProvider;
use Unvurn\Reauth\Providers\OpenIdUserProvider;

class ReauthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
        config([
            'auth.guards.bearer' => array_merge([
                'driver' => 'bearer',
                'provider' => null,
                'modules' => ['access_token']
            ], config('auth.guards.bearer', []))
        ]);

        if (!app()->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/reauth.php', 'reauth');
        }

        app()->singleton('reauth', function ($app) {
            return new Reauth();
        });
    }

    public function boot(): void
    {
        if (app()->runningInConsole()) {
            $this->publishesMigrations([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'reauth-migrations');

            $this->publishes([
                __DIR__ . '/../config/reauth.php' => config_path('reauth.php'),
            ], 'reauth-config');
        }

        //
        $this->configureGuard();
    }

    protected function configureGuard(): void
    {
        Auth::resolved(function ($auth) {
            $auth->extend('bearer', function ($app, $name, array $config) use ($auth) {
                return tap($this->createGuard($auth, $config, new BearerTokenProvider()), function ($guard) {
                    app()->refresh('request', $guard, 'setRequest');
                });
            });
            $auth->extend('json', function ($app, $name, array $config) use ($auth) {
                $decoder = $this->tokenDecoder($app, $config);
                return tap($this->createGuard($auth, $config, new JsonAndKeyPathTokenProvider($config['key']), $decoder), function ($guard) {
                    app()->refresh('request', $guard, 'setRequest');
                });
            });
        });
    }

    protected function createGuard(Factory $auth, array $config, TokenProviderInterface|array $tokenProviders, TokenDecoderInterface|array $tokenDecoders = []): Guard
    {
        $userProvider = $auth->createUserProvider($config['provider'] ?? 'users');
        $request = request();

        if (!is_array($tokenProviders)) {
            $tokenProviders = [$tokenProviders];
        }
        if (!is_array($tokenDecoders)) {
            $tokenDecoders = [$tokenDecoders];
        }

        if (array_key_exists('modules', $config)) {
            foreach ($config['modules'] as $module) {
                switch ($module) {
                    case 'access_token':
                        $userProvider = new AccessTokenUserProvider($userProvider);
                        $tokenDecoders []= new AccessTokenDecoder();
                        break;

                    case 'openid':
                        $userProvider = new OpenidUserProvider($userProvider);
                        // $tokenParsers []= new ...
                        break;

                    case 'android':
                        $userProvider = new AttributionalUserProvider($userProvider);
                        // $tokenParsers []= new ...
                        break;

                    case 'ios':
                        // userProvider = new AttributionalUserProvider($userProvider);
                        // $tokenParsers []= new ...
                        break;
                }
            }
        }

        return new ReauthGuard($userProvider, $request, $tokenProviders, $tokenDecoders);
    }

    private function tokenDecoder($app, $config): TokenDecoderInterface
    {
        $tokenDecoder = Arr::has($config, 'decoder')
            ? $app['reauth']->tokenDecoder(Arr::get($config, 'decoder'))
            : new NullTokenDecoder($config['key']);
        if (is_callable($tokenDecoder)) {
            $tokenDecoder = $tokenDecoder();
        }
        return $tokenDecoder;
    }
}


