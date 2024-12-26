<?php

namespace Modules\Stock\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class StockServiceProvider extends ServiceProvider {
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Stock';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'stock';

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
        $this->app->bind('Modules\Stock\Repositories\StockRepository', function() {
            return new \Modules\Stock\Repositories\Eloquent\EloquentStockRepository(new \Modules\Stock\Entities\Stock());
        });
        $this->app->bind('Modules\Stock\Repositories\StockProductRepository', function() {
            return new \Modules\Stock\Repositories\Eloquent\EloquentStockProductRepository(new \Modules\Stock\Entities\StockProduct());
        });
        $this->app->bind('Modules\Stock\Repositories\StockRoleRepository', function() {
            return new \Modules\Stock\Repositories\Eloquent\EloquentStockRoleRepository(new \Modules\Stock\Entities\StockRole());
        });
        $this->app->bind('Modules\Stock\Repositories\TicketRepository', function() {
            return new \Modules\Stock\Repositories\Eloquent\EloquentTicketRepository(new \Modules\Stock\Entities\Ticket());
        });
        $this->app->bind('Modules\Stock\Repositories\RequestRepository', function() {
            return new \Modules\Stock\Repositories\Eloquent\EloquentRequestRepository(new \Modules\Stock\Entities\Request());
        });
        $this->app->bind('Modules\Stock\Repositories\RequestProductRepository', function() {
            return new \Modules\Stock\Repositories\Eloquent\EloquentRequestProductRepository(new \Modules\Stock\Entities\RequestProduct());
        });
        $this->app->bind('Modules\Stock\Repositories\StoProductRepository', function() {
            return new \Modules\Stock\Repositories\Eloquent\EloquentStoProductRepository(new \Modules\Stock\Entities\StoProduct());
        });
        $this->app->bind('Modules\Stock\Repositories\TicketFileRepository', function() {
            return new \Modules\Stock\Repositories\Eloquent\EloquentTicketFileRepository(new \Modules\Stock\Entities\TicketFile());
        });
        $this->app->bind('Modules\Stock\Repositories\InventoryRepository', function() {
            return new \Modules\Stock\Repositories\Eloquent\EloquentInventoryRepository(new \Modules\Stock\Entities\Inventory());
        });
        $this->app->bind('Modules\Stock\Repositories\InventoryProductRepository', function() {
            return new \Modules\Stock\Repositories\Eloquent\EloquentInventoryProductRepository(new \Modules\Stock\Entities\InventoryProduct());
        });
        $this->app->bind('Modules\Stock\Repositories\TypeRepository', function() {
            return new \Modules\Stock\Repositories\Eloquent\EloquentTypeRepository(new \Modules\Stock\Entities\Type());
        });
    }
}
