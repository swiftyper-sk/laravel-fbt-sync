<?php

namespace Swiftyper\fbt;

use Illuminate\Support\ServiceProvider;
use Swiftyper\fbt\Console\Commands\SwiftyperFbtCommand;

class IntlServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (\config('swiftyper.routes')) {
            $this->loadRoutesFrom(__DIR__ . '/routes.php');
        }

        $this->publishes([
            \dirname(__DIR__, 1) . '/config/swiftyper.php' => config_path('swiftyper.php'),
        ], 'swiftyper-config');

        $this->commands([
            SwiftyperFbtCommand::class,
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            \dirname(__DIR__, 1) . '/config/swiftyper.php',
            'swiftyper'
        );
    }
}
