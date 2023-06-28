<?php

namespace Hanoivip\Misc;

use Illuminate\Support\ServiceProvider;

class ModServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../views' => resource_path('views/vendor/hanoivip'),
            __DIR__.'/../resources/langs' => resource_path('lang/vendor/hanoivip'),
            __DIR__.'/../resources/images' => public_path('images'),
        ]);
        //$this->loadMigrationsFrom(__DIR__ . '/../migrations');
        $this->loadViewsFrom(__DIR__ . '/../views', 'hanoivip');
        //$this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        //$this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        $this->loadTranslationsFrom( __DIR__.'/../resources/langs', 'hanoivip.misc');
        //$this->mergeConfigFrom( __DIR__.'/../config/quest.php', 'quest');
    }

    public function register()
    {
    }
}
