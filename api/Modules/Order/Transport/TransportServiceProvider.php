<?php namespace Modules\Order\Transport;

use Illuminate\Support\ServiceProvider;

class TransportServiceProvider extends ServiceProvider {

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot() {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app->singleton('transport', function($app) {
            return new Transport();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return [];
    }
}
