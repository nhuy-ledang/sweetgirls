<?php namespace Modules\Product\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class ProductServiceProvider extends ServiceProvider {
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Product';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'product';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot() {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app->register(RouteServiceProvider::class);
        $this->registerBindings();
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig() {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower);
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews() {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations() {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories() {
        if (!app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(module_path($this->moduleName, 'Database/factories'));
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return [];
    }

    private function getPublishableViewPaths(): array {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }

    private function registerBindings() {
        $this->app->bind('Modules\Product\Repositories\CategoryRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentCategoryRepository(new \Modules\Product\Entities\Category());
        });
        $this->app->bind('Modules\Product\Repositories\ManufacturerRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentManufacturerRepository(new \Modules\Product\Entities\Manufacturer());
        });
        $this->app->bind('Modules\Product\Repositories\ProductRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductRepository(new \Modules\Product\Entities\Product());
        });
        $this->app->bind('Modules\Product\Repositories\ProductImageRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductImageRepository(new \Modules\Product\Entities\ProductImage());
        });
        $this->app->bind('Modules\Product\Repositories\ProductSpecialRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductSpecialRepository(new \Modules\Product\Entities\ProductSpecial());
        });
        $this->app->bind('Modules\Product\Repositories\ProductQuantityRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductQuantityRepository(new \Modules\Product\Entities\ProductQuantity());
        });
        $this->app->bind('Modules\Product\Repositories\ProductSpecRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductSpecRepository(new \Modules\Product\Entities\ProductSpec());
        });
        $this->app->bind('Modules\Product\Repositories\ProductSpecDescRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductSpecDescRepository(new \Modules\Product\Entities\ProductSpecDesc());
        });
        $this->app->bind('Modules\Product\Repositories\OptionRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentOptionRepository(new \Modules\Product\Entities\Option());
        });
        $this->app->bind('Modules\Product\Repositories\OptionValueRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentOptionValueRepository(new \Modules\Product\Entities\OptionValue());
        });
        $this->app->bind('Modules\Product\Repositories\ProductOptionRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductOptionRepository(new \Modules\Product\Entities\ProductOption());
        });
        $this->app->bind('Modules\Product\Repositories\ProductOptionValueRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductOptionValueRepository(new \Modules\Product\Entities\ProductOptionValue());
        });
        $this->app->bind('Modules\Product\Repositories\ProductVariantRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductVariantRepository(new \Modules\Product\Entities\ProductVariant());
        });
    }
}
