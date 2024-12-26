<?php namespace Modules\Page\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class PageServiceProvider extends ServiceProvider {
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Page';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'page';

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
        $this->app->bind('Modules\Page\Repositories\WidgetRepository', function() {
            return new \Modules\Page\Repositories\Eloquent\EloquentWidgetRepository(new \Modules\Page\Entities\Widget());
        });
        $this->app->bind('Modules\Page\Repositories\ModuleRepository', function() {
            return new \Modules\Page\Repositories\Eloquent\EloquentModuleRepository(new \Modules\Page\Entities\Module());
        });
        $this->app->bind('Modules\Page\Repositories\ModuleDescRepository', function() {
            return new \Modules\Page\Repositories\Eloquent\EloquentModuleDescRepository(new \Modules\Page\Entities\ModuleDesc());
        });
        $this->app->bind('Modules\Page\Repositories\CategoryRepository', function() {
            return new \Modules\Page\Repositories\Eloquent\EloquentCategoryRepository(new \Modules\Page\Entities\Category());
        });
        $this->app->bind('Modules\Page\Repositories\PageRepository', function() {
            return new \Modules\Page\Repositories\Eloquent\EloquentPageRepository(new \Modules\Page\Entities\Page());
        });
        $this->app->bind('Modules\Page\Repositories\PageDescRepository', function() {
            return new \Modules\Page\Repositories\Eloquent\EloquentPageDescRepository(new \Modules\Page\Entities\PageDesc());
        });
        $this->app->bind('Modules\Page\Repositories\PageContentRepository', function() {
            return new \Modules\Page\Repositories\Eloquent\EloquentPageContentRepository(new \Modules\Page\Entities\PageContent());
        });
        $this->app->bind('Modules\Page\Repositories\PageContentDescRepository', function() {
            return new \Modules\Page\Repositories\Eloquent\EloquentPageContentDescRepository(new \Modules\Page\Entities\PageContentDesc());
        });
        $this->app->bind('Modules\Page\Repositories\MenuRepository', function() {
            return new \Modules\Page\Repositories\Eloquent\EloquentMenuRepository(new \Modules\Page\Entities\Menu());
        });
        $this->app->bind('Modules\Page\Repositories\MenuDescRepository', function() {
            return new \Modules\Page\Repositories\Eloquent\EloquentMenuDescRepository(new \Modules\Page\Entities\MenuDesc());
        });
        $this->app->bind('Modules\Page\Repositories\InformationRepository', function() {
            return new \Modules\Page\Repositories\Eloquent\EloquentInformationRepository(new \Modules\Page\Entities\Information());
        });
        $this->app->bind('Modules\Page\Repositories\InformationDescRepository', function() {
            return new \Modules\Page\Repositories\Eloquent\EloquentInformationDescRepository(new \Modules\Page\Entities\InformationDesc());
        });
        $this->app->bind('Modules\Page\Repositories\LayoutRepository', function() {
            return new \Modules\Page\Repositories\Eloquent\EloquentLayoutRepository(new \Modules\Page\Entities\Layout());
        });
        $this->app->bind('Modules\Page\Repositories\LayoutDescRepository', function() {
            return new \Modules\Page\Repositories\Eloquent\EloquentLayoutDescRepository(new \Modules\Page\Entities\LayoutDesc());
        });
        $this->app->bind('Modules\Page\Repositories\LayoutModuleRepository', function() {
            return new \Modules\Page\Repositories\Eloquent\EloquentLayoutModuleRepository(new \Modules\Page\Entities\LayoutModule());
        });
        $this->app->bind('Modules\Page\Repositories\LayoutModuleDescRepository', function() {
            return new \Modules\Page\Repositories\Eloquent\EloquentLayoutModuleDescRepository(new \Modules\Page\Entities\LayoutModuleDesc());
        });
        $this->app->bind('Modules\Page\Repositories\LayoutPatternRepository', function() {
            return new \Modules\Page\Repositories\Eloquent\EloquentLayoutPatternRepository(new \Modules\Page\Entities\LayoutPattern());
        });
        $this->app->bind('Modules\Page\Repositories\LayoutPatternDescRepository', function() {
            return new \Modules\Page\Repositories\Eloquent\EloquentLayoutPatternDescRepository(new \Modules\Page\Entities\LayoutPatternDesc());
        });
        $this->app->bind('Modules\Page\Repositories\PageModuleRepository', function() {
            return new \Modules\Page\Repositories\Eloquent\EloquentPageModuleRepository(new \Modules\Page\Entities\PageModule());
        });
        $this->app->bind('Modules\Page\Repositories\PageModuleDescRepository', function() {
            return new \Modules\Page\Repositories\Eloquent\EloquentPageModuleDescRepository(new \Modules\Page\Entities\PageModuleDesc());
        });
    }
}
