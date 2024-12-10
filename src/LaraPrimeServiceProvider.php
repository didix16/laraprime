<?php

namespace Didix16\LaraPrime;

use Didix16\LaraPrime\Commands\DevCommand;
use Didix16\LaraPrime\Commands\InstallCommand;
use Didix16\LaraPrime\Commands\PublishCommand;
use Didix16\LaraPrime\Commands\ThemesCommand;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LaraPrimeServiceProvider extends ServiceProvider
{
    /**
     * The available commands only when running in console.
     * @var array
     */
    protected array $consoleCommands = [
        InstallCommand::class,
        PublishCommand::class,
        DevCommand::class,
        ThemesCommand::class,
    ];

    /**
     * The available global commands (console and web).
     * @var array
     */
    protected array $globalCommands = [];
    /**
     * Bootstrap the package services.
     * @return void
     */
    public function boot(): void
    {
        $this
            ->registerViews()
            ->registerTranslations();

        if($this->app->runningInConsole()) {

            AboutCommand::add('LaraPrime', fn () => [
                'Version'       => LaraPrime::version(),
                'Domain'        => config('laraprime.domain'),
                'Prefix'        => config('laraprime.prefix'),
                'Assets Status' => LaraPrime::assetsAreCurrent() ? '<fg=green;options=bold>CURRENT</>' : '<fg=yellow;options=bold>OUTDATED</>',
            ]);

            $this
                ->registerMigrationsPublisher()
                ->registerConfigPublisher()
                ->registerViewsPublisher()
                ->registerTranslationsPublisher()
                ->registerAssetsPublisher()
                ->registerLaraPrimePublisher()
                ->commands($this->consoleCommands);
        }
        $this
            ->registerRoutes()
            ->commands($this->globalCommands);
    }
    public function register(): void
    {

        $provider = config('laraprime.provider', \App\LaraPrime\LaraPrimeProvider::class);

        if ($provider !== null && class_exists($provider)) {
            $this->app->register($provider);
        }

        $this->mergeConfigFrom(
            LaraPrime::packagePath('config/laraprime.php'),
            'laraprime'
        );
    }

    protected function registerViewsPublisher(): self
    {
        $this->publishes([
            LaraPrime::packagePath('resources/views') => resource_path('views/vendor/laraprime'),
        ], 'laraprime-views');

        return $this;
    }

    /**
     * Register config publishing.
     * @return self
     */
    protected function registerConfigPublisher(): self
    {
        $this->publishes([
            LaraPrime::packagePath('config/laraprime.php') => config_path('laraprime.php'),
        ], 'laraprime-config');

        return $this;
    }

    protected function registerMigrationsPublisher(): self
    {
        $this->publishes([
            LaraPrime::packagePath('database/migrations') => database_path('migrations'),
        ], 'laraprime-migrations');

        return $this;
    }


    /**
     * Register translations.
     *
     * @return $this
     */
    protected function registerTranslationsPublisher(): self
    {
        $this->publishes([
            LaraPrime::packagePath('resources/lang') => lang_path('vendor/laraprime'),
        ], 'laraprime-translations');

        return $this;
    }

    /**
     * Register LaraPrime to the application.
     * @return $this
     */
    protected function registerLaraPrimePublisher(): self
    {
        $this->publishes([
            LaraPrime::packagePath('stubs/app/routes') => base_path('routes'),
            LaraPrime::packagePath('stubs/app/LaraPrime') => app_path('LaraPrime'),
        ], 'laraprime-app-stubs');

        return $this;
    }

    /**
     * Register the asset publishing configuration.
     * @return $this
     */
    protected function registerAssetsPublisher(): self
    {
        $this->publishes([
            LaraPrime::packagePath('build') => public_path('vendor/laraprime'),
            LaraPrime::packagePath('resources/themes/lara-light-cyan') => public_path('vendor/laraprime/themes/lara-light-cyan'),
            LaraPrime::packagePath('resources/themes/lara-dark-cyan') => public_path('vendor/laraprime/themes/lara-dark-cyan'),
        ], ['laraprime-assets', 'laravel-assets']);

        return $this;
    }

    /**
     * Register views & Publish views.
     *
     * @return $this
     */
    public function registerViews(): self
    {
        $this->loadViewsFrom(LaraPrime::packagePath('resources/views'), 'laraprime');

        return $this;
    }

    /**
     * Register the package translations.
     * @return self
     */
    public function registerTranslations(): self
    {
        $this->loadJsonTranslationsFrom(LaraPrime::packagePath('resources/lang'));

        return $this;
    }

    /**
     * Register the package routes.
     *
     * @return self
     */
    protected function registerRoutes(): self
    {
        Route::group([
            'domain' => config('laraprime.domain', null),
            'as' => 'laraprime.api',
            'prefix' => 'prime-api',
            'middleware' => 'laraprime:api',
            'excluded_middleware' => [SubstituteBindings::class],
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });

        return $this;
    }
}
