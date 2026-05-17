<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Tests;

use BjTheCod3r\Deezer\DeezerServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            DeezerServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('deezer.endpoints.api', 'https://api.deezer.com');
    }
}
