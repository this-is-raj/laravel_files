<?php
/**
 * Created by PhpStorm.
 * User: froiden
 * Date: 10/15/18
 * Time: 11:52 AM
 */
namespace Raj\LaravelFiles;

use Illuminate\Support\ServiceProvider;

class LaravelFileServiceProvide extends ServiceProvider
{
    /**
     * Check to see if we're using lumen or laravel.
     *
     * @return bool
     */
    public function isLumen () {
        $lumenClass = 'Laravel\Lumen\Application';
        return ($this->app instanceof $lumenClass);
    }

    public function boot () {
        $this->publishConfig();

        $this->registerMigrations();

        $this->registerRoutes();

        $this->registerBladeDirectives();

        $this->publishViews();
    }

    /**
     * Register bindings into the container.
     *
     * @return void
     */
    public function register () {
        //
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * Publish Configurations file.
     */
    private function publishConfig () {
        if ($this->isLumen()) return;

        $configSource = realpath(__DIR__ . '/config.php');
        $configDestination = config_path('laravel_files.php');

        $this->mergeConfigFrom($configSource, 'laravel_files');
        $this->publishes([$configSource, $configDestination], 'config');
    }

    /**
     * Register all the migrations.
     */
    private function registerMigrations () {
        $migrationsPath = __DIR__ . '/Database/migrations';

        $this->loadMigrationsFrom($migrationsPath);
    }

    private function registerRoutes () {
        require_once 'routes.php';
    }

    private function registerBladeDirectives () {

    }

    private function publishViews () {
        if ($this->isLumen()) return;

        $viewPath = __DIR__ . '/view';

        $this->loadMigrationsFrom($viewPath);
    }
}
