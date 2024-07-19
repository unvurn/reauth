<?php

declare(strict_types=1);

namespace Unvurn\Reauth;

use Illuminate\Contracts\Auth\Factory;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Unvurn\Reauth\Auth\Token\BearerTokenProvider;
use Unvurn\Reauth\Auth\Token\JsonAndKeyPathTokenProvider;
use Unvurn\Reauth\Auth\Token\TokenProviderInterface;

class ReauthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (!app()->configurationIsCached()) {
            $this->mergeConfigFromFile( __DIR__.'/../config/auth.php', 'auth');
            $this->mergeConfigFromFile(__DIR__.'/../config/reauth.php', 'reauth');
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
                return tap($this->createGuard('bearer', $auth, $config), function ($guard) {
                    app()->refresh('request', $guard, 'setRequest');
                });
            });
            $auth->extend('json', function ($app, $name, array $config) use ($auth) {
                return tap($this->createGuard('json', $auth, $config), function ($guard) {
                    app()->refresh('request', $guard, 'setRequest');
                });
            });
        });
    }

    protected function createGuard(string $type, Factory $auth, array $config): Guard
    {
        $originalUserProvider = $auth->createUserProvider($config['provider'] ?? 'users');
        $request = request();
        $tokenProvider = null;

        if ($type === 'bearer') {
            $tokenProvider = new BearerTokenProvider();
        }

        $specs = [];
        if (array_key_exists('pipelines', $config)) {
            foreach ($config['pipelines'] as $key => $keyedConfig) {
                if ($type === 'json') {
                    $tokenProvider = new JsonAndKeyPathTokenProvider($key);
                }

                $tokenResolver = is_callable($keyedConfig['resolver']) ? $keyedConfig['resolver'] : new ($keyedConfig['resolver'])();
                if (!($tokenProvider instanceof TokenProviderInterface) || is_null($tokenResolver)) {
                    continue;
                }
                $userProvider = isset($keyedConfig['users'])
                    ? new ($keyedConfig['users'])($originalUserProvider)
                    : $originalUserProvider;

                $specs[] = new GuardPipeline(
                    $tokenProvider,
                    $tokenResolver,
                    $userProvider,
                );
            }
        }

        return new ReauthGuard($originalUserProvider, $request, $specs);
    }

    private function mergeConfigFromFile(string $path, string $basekey): void {
        $newAuthConfig = require $path;

        foreach ($newAuthConfig as $name => $subc) {
            $targetKey = $basekey . '.' . $name;
            $subc0 = config($targetKey);

            config([
                $targetKey =>
                    (is_array($subc0)
                        ? array_merge(
                            $subc,
                            config($targetKey, []))
                        : $subc ?? $subc0)
            ]);
        }
    }
}
