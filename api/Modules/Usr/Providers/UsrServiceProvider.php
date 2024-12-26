<?php

namespace Modules\Usr\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class UsrServiceProvider extends ServiceProvider {
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Usr';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'usr';

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
        $this->registerSentinel();
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
        $this->app->bind('Modules\Usr\Repositories\GroupRepository', function() {
            return new \Modules\Usr\Repositories\Eloquent\EloquentGroupRepository(new \Modules\Usr\Entities\Group());
        });
        $this->app->bind('Modules\Usr\Repositories\RoleRepository', function() {
            return new \Modules\Usr\Sentinel\RoleRepository(new \Modules\Usr\Entities\Sentinel\Role());
        });
        $this->app->bind('Modules\Usr\Repositories\NotifyRepository', function() {
            return new \Modules\Usr\Repositories\Eloquent\EloquentNotifyRepository(new \Modules\Usr\Entities\Notify());
        });
        $this->app->bind('Modules\Usr\Repositories\ActivityRepository', function() {
            return new \Modules\Usr\Repositories\Eloquent\EloquentActivityRepository(new \Modules\Usr\Entities\Activity());
        });
    }

    /**
     * Registers sentinel.
     *
     * @return void
     */
    private function registerSentinel() {
        $user_repository = new \Modules\Usr\Sentinel\UserRepository(new \Modules\Usr\Entities\Sentinel\User());
        $this->app->bind('Modules\Usr\Repositories\Authentication', "Modules\\Usr\\Sentinel\\Authentication");
        $this->app->bind('Modules\Usr\Repositories\UserRepository', function() use ($user_repository) {
            return $user_repository;
        });
        $this->app->singleton('usr.sentinel', function($app) use ($user_repository) {
            $config = $app['config']->get('cartalyst.sentinel.persistences');
            $sentinel = new \Modules\Usr\Sentinel\Sentinel(
                new \Modules\Usr\Sentinel\PersistenceRepository($app['sentinel.session'], $app['sentinel.cookie'], new \Modules\Usr\Entities\Sentinel\Persistence(), $config['single']),
                $user_repository,
                new \Modules\Usr\Sentinel\RoleRepository(new \Modules\Usr\Entities\Sentinel\Role()),
                $app['events']
            );
            $sentinel->setThrottleRepository(new \Modules\Usr\Sentinel\ThrottleRepository(new \Modules\Usr\Entities\Sentinel\Throttle()));
            return $sentinel;
        });
        $this->app->singleton('usr.sentinel.activations', function($app) {
            $config = $app['config']->get('cartalyst.sentinel.activations');
            return new \Modules\Usr\Sentinel\ActivationRepository('Modules\Usr\Entities\Sentinel\Activation', $config['expires']);
        });
        $this->app->singleton('usr.sentinel.reminders', function($app) use ($user_repository) {
            $config = $app['config']->get('cartalyst.sentinel.reminders');
            return new \Modules\Usr\Sentinel\ReminderRepository($user_repository, 'Modules\Usr\Entities\Sentinel\Reminder', $config['expires']);
        });
    }
}
