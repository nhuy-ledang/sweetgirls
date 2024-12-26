<?php

namespace Modules\System\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class SystemServiceProvider extends ServiceProvider {
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'System';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'system';

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
        $this->app->bind('Modules\System\Repositories\BannerRepository', function() {
            return new \Modules\System\Repositories\Eloquent\EloquentBannerRepository(new \Modules\System\Entities\Banner());
        });
        $this->app->bind('Modules\System\Repositories\BannerImageRepository', function() {
            return new \Modules\System\Repositories\Eloquent\EloquentBannerImageRepository(new \Modules\System\Entities\BannerImage());
        });
        $this->app->bind('Modules\System\Repositories\SettingRepository', function() {
            return new \Modules\System\Repositories\Eloquent\EloquentSettingRepository(new \Modules\System\Entities\Setting());
        });
        $this->app->bind('Modules\System\Repositories\ContactRepository', function() {
            return new \Modules\System\Repositories\Eloquent\EloquentContactRepository(new \Modules\System\Entities\Contact());
        });
        $this->app->bind('Modules\System\Repositories\TranslateRepository', function() {
            return new \Modules\System\Repositories\Eloquent\EloquentTranslateRepository(new \Modules\System\Entities\Translate());
        });
        $this->app->bind('Modules\System\Repositories\LanguageRepository', function() {
            return new \Modules\System\Repositories\Eloquent\EloquentLanguageRepository(new \Modules\System\Entities\Language());
        });
        $this->app->bind('Modules\System\Repositories\FeedbackRepository', function () {
            return new \Modules\System\Repositories\Eloquent\EloquentFeedbackRepository(new \Modules\System\Entities\Feedback());
        });
        $this->app->bind('Modules\System\Repositories\VisitorRepository', function () {
            return new \Modules\System\Repositories\Eloquent\EloquentVisitorRepository(new \Modules\System\Entities\Visitor());
        });
        /*$this->app->bind('Modules\System\Repositories\StatisticRepository', function() {
            return new \Modules\System\Repositories\Eloquent\EloquentStatisticRepository(new \Modules\System\Entities\Statistic());
        });*/
    }
}
