<?php

declare(strict_types=1);

namespace Unvurn\Reauth\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class UserMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new User model';

    protected $signature = 'make:user {name} {--model=User}';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'User';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/stubs/user.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Models';
    }
}
