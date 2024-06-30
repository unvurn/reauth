<?php

declare(strict_types=1);

namespace Unvurn\Reauth;

use Illuminate\Support\ServiceProvider;
use Unvurn\Reauth\Console\Commands\UserMakeCommand;

class ReauthCommandServiceProvider extends ServiceProvider
{
    protected array $commands = [
        UserMakeCommand::class,
    ];

    public function register(): void
    {
        $this->commands($this->commands);
    }
}
