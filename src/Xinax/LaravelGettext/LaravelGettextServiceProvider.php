<?php

namespace Xinax\LaravelGettext;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Xinax\LaravelGettext\Adapters\AdapterInterface;
use Xinax\LaravelGettext\Adapters\LaravelAdapter;
use Xinax\LaravelGettext\Commands\GettextCreate;
use Xinax\LaravelGettext\Commands\GettextUpdate;
use Xinax\LaravelGettext\Config\ConfigManager;
use Xinax\LaravelGettext\Translators\Gettext;
use Xinax\LaravelGettext\Translators\Symfony;

/**
 * Main service provider
 *
 * Class LaravelGettextServiceProvider
 * @package Xinax\LaravelGettext
 */
class LaravelGettextServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('laravel-gettext.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/config.php', 'laravel-gettext');

        $this->app->bind(AdapterInterface::class, function (Application $app) {
            return $app->make(ConfigManager::create()->get()->getAdapter());
        });

        // Main class register
        $this->app->singleton('laravel-gettext', function (Application $app) {
            $configuration = ConfigManager::create()->get();
            $fileSystem = new FileSystem($configuration, app_path(), storage_path());
            $adapter = $app->make(AdapterInterface::class);

            if ('symfony' == $configuration->getHandler()) {
                // symfony translator implementation
                $translator = new Symfony($configuration, $adapter, $fileSystem);
            } else {
                // GNU/Gettext php extension
                $translator = new Gettext($configuration, $adapter, $fileSystem);
            }

            return new LaravelGettext($translator);
        });

        $this->app->alias('laravel-gettext', LaravelGettext::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                GettextCreate::class,
                GettextUpdate::class,
            ]);
        }
    }

    /**
     * Get the services
     *
     * @return array
     */
    public function provides()
    {
        return [
            'laravel-gettext',
            LaravelGettext::class,
        ];
    }
}
