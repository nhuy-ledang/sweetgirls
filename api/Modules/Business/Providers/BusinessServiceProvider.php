<?php

namespace Modules\Business\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class BusinessServiceProvider extends ServiceProvider {
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Business';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'business';

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
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
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
        $this->app->bind('Modules\Business\Repositories\CategoryRepository', function() {
            return new \Modules\Business\Repositories\Eloquent\EloquentCategoryRepository(new \Modules\Business\Entities\Category());
        });
        $this->app->bind('Modules\Business\Repositories\ProductRepository', function() {
            return new \Modules\Business\Repositories\Eloquent\EloquentProductRepository(new \Modules\Business\Entities\Product());
        });
        $this->app->bind('Modules\Business\Repositories\ImportRepository', function() {
            return new \Modules\Business\Repositories\Eloquent\EloquentImportRepository(new \Modules\Business\Entities\Import());
        });
        $this->app->bind('Modules\Business\Repositories\ImportHistoryRepository', function() {
            return new \Modules\Business\Repositories\Eloquent\EloquentImportHistoryRepository(new \Modules\Business\Entities\ImportHistory());
        });
        $this->app->bind('Modules\Business\Repositories\PolicyGroupRepository', function() {
            return new \Modules\Business\Repositories\Eloquent\EloquentPolicyGroupRepository(new \Modules\Business\Entities\PolicyGroup());
        });
        $this->app->bind('Modules\Business\Repositories\PolicyRepository', function() {
            return new \Modules\Business\Repositories\Eloquent\EloquentPolicyRepository(new \Modules\Business\Entities\Policy());
        });
        $this->app->bind('Modules\Business\Repositories\PromoRepository', function() {
            return new \Modules\Business\Repositories\Eloquent\EloquentPromoRepository(new \Modules\Business\Entities\Promo());
        });
        $this->app->bind('Modules\Business\Repositories\SupplierCategoryRepository', function() {
            return new \Modules\Business\Repositories\Eloquent\EloquentSupplierCategoryRepository(new \Modules\Business\Entities\SupplierCategory());
        });
        $this->app->bind('Modules\Business\Repositories\SupplierGroupRepository', function() {
            return new \Modules\Business\Repositories\Eloquent\EloquentSupplierGroupRepository(new \Modules\Business\Entities\SupplierGroup());
        });
        $this->app->bind('Modules\Business\Repositories\SupplierRepository', function() {
            return new \Modules\Business\Repositories\Eloquent\EloquentSupplierRepository(new \Modules\Business\Entities\Supplier());
        });
        $this->app->bind('Modules\Business\Repositories\SupplierContactRepository', function() {
            return new \Modules\Business\Repositories\Eloquent\EloquentSupplierContactRepository(new \Modules\Business\Entities\SupplierContact());
        });
        $this->app->bind('Modules\Business\Repositories\SupplierNoteRepository', function() {
            return new \Modules\Business\Repositories\Eloquent\EloquentSupplierNoteRepository(new \Modules\Business\Entities\SupplierNote());
        });
    }
}
