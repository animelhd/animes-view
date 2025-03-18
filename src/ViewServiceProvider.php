<?php

namespace Animelhd\AnimesView;

use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            \dirname(__DIR__).'/config/animesview.php' => config_path('animesview.php'),
        ], 'view-config');

        $this->publishes([
            \dirname(__DIR__).'/migrations/' => database_path('migrations'),
        ], 'view-migrations');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            \dirname(__DIR__).'/config/animesview.php',
            'view'
        );
    }
}
