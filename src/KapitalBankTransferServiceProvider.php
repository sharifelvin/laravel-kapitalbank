<?php

namespace SharifElvin\KapitalBankTransfer;

use Illuminate\Support\ServiceProvider;

class KapitalBankTransferServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'kapital-bank-transfer');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'kapital-bank-transfer');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('kapital-bank-transfer.php'),
            ], 'config');

            if (! class_exists('CreatePaymentsTable')) {
                $this->publishes([
                  __DIR__ . '/./database/migrations/2021_01_01_10000_create_payments_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_payments_table.php'),
                  // you can add any number of migrations here
                ], 'migrations');
            }

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/kapital-bank-transfer'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/kapital-bank-transfer'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/kapital-bank-transfer'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'kapital-bank-transfer');

        // Register the main class to use with the facade
        $this->app->singleton('kapital-bank-transfer', function () {
            return new KapitalBankTransfer;
        });
    }
}
