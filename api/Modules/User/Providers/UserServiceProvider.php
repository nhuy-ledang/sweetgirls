<?php

namespace Modules\User\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class UserServiceProvider extends ServiceProvider {
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'User';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'user';

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

        $this->app->singleton('reminder-sms', function($app) {
            $config = $app['config']->get('cartalyst.sentinel.reminders');

            return new \Modules\User\Repositories\Sentinel\SentinelReminderSMSRepository($app['sentinel.users'], 'Modules\User\Entities\ReminderSMS', $config['expires']);
        });
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
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/users.php'), 'user.users'
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

        $driver = config('user.users.driver', 'Sentinel');

        $this->app->bind('Modules\User\Repositories\UserRepository', "Modules\\User\\Repositories\\{$driver}\\{$driver}UserRepository");
        $this->app->bind('Modules\User\Repositories\RoleRepository', "Modules\\User\\Repositories\\{$driver}\\{$driver}RoleRepository");
        $this->app->bind('Modules\User\Repositories\Authentication', "Modules\\User\\Repositories\\{$driver}\\{$driver}Authentication");

        $this->app->bind('Modules\User\Repositories\GroupRepository', function() {
            return new \Modules\User\Repositories\Eloquent\EloquentGroupRepository(new \Modules\User\Entities\Group());
        });
        $this->app->bind('Modules\User\Repositories\ReminderPhoneNumberRepository', function() {
            return new \Modules\User\Repositories\Eloquent\EloquentReminderPhoneNumberRepository(new \Modules\User\Entities\ReminderPhoneNumber());
        });
        $this->app->bind('Modules\User\Repositories\SocialRepository', function() {
            return new \Modules\User\Repositories\Eloquent\EloquentSocialRepository(new \Modules\User\Entities\Social());
        });
        $this->app->bind('Modules\User\Repositories\DeviceTokenRepository', function() {
            return new \Modules\User\Repositories\Eloquent\EloquentDeviceTokenRepository(new \Modules\User\Entities\DeviceToken());
        });
        $this->app->bind('Modules\User\Repositories\ReminderLogRepository', function() {
            return new \Modules\User\Repositories\Eloquent\EloquentReminderLogRepository(new \Modules\User\Entities\ReminderLog());
        });
        $this->app->bind('Modules\User\Repositories\AddressRepository', function() {
            return new \Modules\User\Repositories\Eloquent\EloquentAddressRepository(new \Modules\User\Entities\Address());
        });
        $this->app->bind('Modules\User\Repositories\TicketRepository', function() {
            return new \Modules\User\Repositories\Eloquent\EloquentTicketRepository(new \Modules\User\Entities\Ticket());
        });
        $this->app->bind('Modules\User\Repositories\LeadSourceRepository', function() {
            return new \Modules\User\Repositories\Eloquent\EloquentLeadSourceRepository(new \Modules\User\Entities\LeadSource());
        });
        $this->app->bind('Modules\User\Repositories\LeadRepository', function() {
            return new \Modules\User\Repositories\Eloquent\EloquentLeadRepository(new \Modules\User\Entities\Lead());
        });
        $this->app->bind('Modules\User\Repositories\UserCoinRepository', function() {
            return new \Modules\User\Repositories\Eloquent\EloquentUserCoinRepository(new \Modules\User\Entities\UserCoin());
        });
        $this->app->bind('Modules\User\Repositories\NotifyRepository', function() {
            return new \Modules\User\Repositories\Eloquent\EloquentNotifyRepository(new \Modules\User\Entities\Notify());
        });
        $this->app->bind('Modules\User\Repositories\UserRankRepository', function() {
            return new \Modules\User\Repositories\Eloquent\EloquentUserRankRepository(new \Modules\User\Entities\UserRank());
        });
        /*$this->app->bind('Modules\User\Repositories\ContactRepository', function() {
            return new \Modules\User\Repositories\Eloquent\EloquentContactRepository(new \Modules\User\Entities\Contact());
        });
        $this->app->bind('Modules\User\Repositories\HistoryRepository', function() {
            $repository = new \Modules\User\Repositories\Eloquent\EloquentHistoryRepository(new \Modules\User\Entities\History());
            if (!config('app.cache')) return $repository;
            return $repository;
        });
        $this->app->bind('Modules\User\Repositories\CollectionRepository', function() {
            $repository = new \Modules\User\Repositories\Eloquent\EloquentCollectionRepository(new \Modules\User\Entities\Collection());
            if (!config('app.cache')) return $repository;
            return $repository;
        });
        $this->app->bind('Modules\User\Repositories\CollectionFolderRepository', function() {
            $repository = new \Modules\User\Repositories\Eloquent\EloquentCollectionFolderRepository(new \Modules\User\Entities\CollectionFolder());
            if (!config('app.cache')) return $repository;
            return $repository;
        });
        $this->app->bind('Modules\User\Repositories\FollowRepository', function() {
            $repository = new \Modules\User\Repositories\Eloquent\EloquentFollowRepository(new \Modules\User\Entities\Follow());
            if (!config('app.cache')) return $repository;
            return $repository;
        });
        $this->app->bind('Modules\User\Repositories\FriendRepository', function() {
            $repository = new \Modules\User\Repositories\Eloquent\EloquentFriendRepository(new \Modules\User\Entities\Friend());
            if (!config('app.cache')) return $repository;
            return $repository;
        });
        $this->app->bind('Modules\User\Repositories\StatsRepository', function() {
            $repository = new \Modules\User\Repositories\Eloquent\EloquentStatsRepository(new \Modules\User\Entities\Stats());
            if (!config('app.cache')) return $repository;
            return $repository;
        });
        $this->app->bind('Modules\User\Repositories\ContactCareerRepository', function() {
            $repository = new \Modules\User\Repositories\Eloquent\EloquentContactCareerRepository(new \Modules\User\Entities\ContactCareer());
            if (!config('app.cache')) return $repository;
            return $repository;
        });
        $this->app->bind('Modules\User\Repositories\ContactLikeRepository', function() {
            $repository = new \Modules\User\Repositories\Eloquent\EloquentContactLikeRepository(new \Modules\User\Entities\ContactLike());
            if (!config('app.cache')) return $repository;
            return $repository;
        });
        $this->app->bind('Modules\User\Repositories\ContactProductRepository', function() {
            $repository = new \Modules\User\Repositories\Eloquent\EloquentContactProductRepository(new \Modules\User\Entities\ContactProduct());
            if (!config('app.cache')) return $repository;
            return $repository;
        });
        $this->app->bind('Modules\User\Repositories\TransactionRepository', function() {
            $repository = new \Modules\User\Repositories\Eloquent\EloquentTransactionRepository(new \Modules\User\Entities\Transaction());
            if (!config('app.cache')) return $repository;
            return $repository;
        });
        $this->app->bind('Modules\User\Repositories\ActivityRepository', function() {
            $repository = new \Modules\User\Repositories\Eloquent\EloquentActivityRepository(new \Modules\User\Entities\Activity());
            if (!config('app.cache')) return $repository;
            return $repository;
        });*/
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
}