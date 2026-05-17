<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer;

use BjTheCod3r\Deezer\Config\DeezerConfig;
use BjTheCod3r\Deezer\Http\DeezerClient;
use Illuminate\Support\ServiceProvider;

class DeezerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/deezer.php', 'deezer');

        $this->app->singleton(DeezerConfig::class, function ($app): DeezerConfig {
            /** @var array<string, mixed> $config */
            $config = $app['config']->get('deezer', []);

            return DeezerConfig::fromArray($config);
        });

        $this->app->singleton(DeezerClient::class, fn ($app): DeezerClient => new DeezerClient(
            config: $app->make(DeezerConfig::class),
        ));

        $this->app->singleton(Deezer::class, fn ($app): Deezer => new Deezer(
            client: $app->make(DeezerClient::class),
            config: $app->make(DeezerConfig::class),
        ));
        $this->app->alias(Deezer::class, 'deezer');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/deezer.php' => $this->app->configPath('deezer.php'),
            ], 'deezer-config');
        }
    }
}
