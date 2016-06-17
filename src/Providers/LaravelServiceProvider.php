<?php namespace Znck\Livre\Providers;

use Illuminate\Support\ServiceProvider;
use Znck\Livre\Factory;

class LaravelServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function boot() {
        $this->mergeConfigFrom(__DIR__.'/../../config/livre.php', 'livre');
        $this->publishes(
            [
                dirname(dirname(__DIR__)).'/config/livre.php' => config_path('livre.php'),
            ]
        );
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->mergeConfigFrom(dirname(dirname(__DIR__)).'/config/livre.php', 'livre');

        $this->app->singleton(
            'livre',
            function () {
                return new Factory(config('livre.providers', []), config('livre.services'));
            }
        );
    }

    public function provides() {
        return ['livre'];
    }
}
