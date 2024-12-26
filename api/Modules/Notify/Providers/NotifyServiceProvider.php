<?php

namespace Modules\Notify\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class NotifyServiceProvider extends ServiceProvider {
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Notify';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'notify';

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
    public function registerViews()
    {
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
    public function registerTranslations()
    {
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
    public function registerFactories()
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(module_path($this->moduleName, 'Database/factories'));
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }

    private function registerBindings() {
        $this->app->bind('Modules\Notify\Repositories\NotificationRepository', function() {
            $repository = new \Modules\Notify\Repositories\Eloquent\EloquentNotificationRepository(new \Modules\Notify\Entities\Notification());
            if (!config('app.cache')) return $repository;
            return $repository;
        });

        $this->app->bind('Modules\Notify\Repositories\ContactRepository', function() {
            $repository = new \Modules\Notify\Repositories\Eloquent\EloquentContactRepository(new \Modules\Notify\Entities\Contact());
            if (!config('app.cache')) return $repository;
            return $repository;
        });

        $this->app->bind('Modules\Notify\Repositories\FeedbackRepository', function() {
            $repository = new \Modules\Notify\Repositories\Eloquent\EloquentFeedbackRepository(new \Modules\Notify\Entities\Feedback());
            if (!config('app.cache')) return $repository;
            return $repository;
        });

        $this->app->bind('Modules\Notify\Repositories\MessageRepository', function() {
            $repository = new \Modules\Notify\Repositories\Eloquent\EloquentMessageRepository(new \Modules\Notify\Entities\Message());
            if (!config('app.cache')) return $repository;
            return $repository;
        });
    }
}
