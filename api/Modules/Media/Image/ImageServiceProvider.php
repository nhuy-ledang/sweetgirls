<?php namespace Modules\Media\Image;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Modules\Media\Image\Intervention\InterventionFactory;

class ImageServiceProvider extends ServiceProvider {
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app->bind(
            'Modules\Media\Image\ImageFactoryInterface',
            'Modules\Media\Image\Intervention\InterventionFactory'
        );

        // Create image
        $this->app->singleton('imagy', function ($app) {
            $factory = new InterventionFactory();
            $thumbnailManager = new ThumbnailsManager($app['config']);

            return new Imagy($factory, $thumbnailManager, $app['config']);
        });

        $this->app->booting(function () {
            $loader = AliasLoader::getInstance();
            $loader->alias('Imagy', 'Modules\Media\Image\Facade\Imagy');
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return ['imagy'];
    }
}
