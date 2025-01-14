<?php

namespace Modules\Order\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class OrderServiceProvider extends ServiceProvider {
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Order';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'order';

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
            $sourcePath => $viewPath
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
        $this->app->bind('Modules\Order\Repositories\CartRepository', function() {
            return new \Modules\Order\Repositories\Eloquent\EloquentCartRepository(new \Modules\Order\Entities\Cart());
        });
        $this->app->bind('Modules\Order\Repositories\OrderRepository', function() {
            return new \Modules\Order\Repositories\Eloquent\EloquentOrderRepository(new \Modules\Order\Entities\Order());
        });
        $this->app->bind('Modules\Order\Repositories\OrderProductRepository', function() {
            return new \Modules\Order\Repositories\Eloquent\EloquentOrderProductRepository(new \Modules\Order\Entities\OrderProduct());
        });
        $this->app->bind('Modules\Order\Repositories\OrderTotalRepository', function() {
            return new \Modules\Order\Repositories\Eloquent\EloquentOrderTotalRepository(new \Modules\Order\Entities\OrderTotal());
        });
        $this->app->bind('Modules\Order\Repositories\OrderHistoryRepository', function() {
            return new \Modules\Order\Repositories\Eloquent\EloquentOrderHistoryRepository(new \Modules\Order\Entities\OrderHistory());
        });
        $this->app->bind('Modules\Order\Repositories\OrderTagsRepository', function() {
            return new \Modules\Order\Repositories\Eloquent\EloquentOrderTagsRepository(new \Modules\Order\Entities\OrderTags());
        });
        $this->app->bind('Modules\Order\Repositories\WebhookRepository', function () {
            return new \Modules\Order\Repositories\Eloquent\EloquentWebhookRepository(new \Modules\Order\Entities\Webhook());
        });
        $this->app->bind('Modules\Order\Repositories\InvoiceVatRepository', function() {
            return new \Modules\Order\Repositories\Eloquent\EloquentInvoiceVatRepository(new \Modules\Order\Entities\InvoiceVat());
        });
    }
}
