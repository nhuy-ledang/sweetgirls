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
        $this->app->bind('Modules\Product\Repositories\CategoryDescRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentCategoryDescRepository(new \Modules\Product\Entities\CategoryDesc());
        });
        $this->app->bind('Modules\Product\Repositories\CategoryModuleRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentCategoryModuleRepository(new \Modules\Product\Entities\CategoryModule());
        });
        $this->app->bind('Modules\Product\Repositories\CategoryModuleDescRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentCategoryModuleDescRepository(new \Modules\Product\Entities\CategoryModuleDesc());
        });
        $this->app->bind('Modules\Product\Repositories\ManufacturerRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentManufacturerRepository(new \Modules\Product\Entities\Manufacturer());
        });
        $this->app->bind('Modules\Product\Repositories\ProductRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductRepository(new \Modules\Product\Entities\Product());
        });
        $this->app->bind('Modules\Product\Repositories\ProductDescRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductDescRepository(new \Modules\Product\Entities\ProductDesc());
        });
        $this->app->bind('Modules\Product\Repositories\ProductLikeRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductLikeRepository(new \Modules\Product\Entities\ProductLike());
        });
        $this->app->bind('Modules\Product\Repositories\ProductLatestRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductLatestRepository(new \Modules\Product\Entities\ProductLatest());
        });
        $this->app->bind('Modules\Product\Repositories\ProductBestsellerRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductBestsellerRepository(new \Modules\Product\Entities\ProductBestseller());
        });
        $this->app->bind('Modules\Product\Repositories\ProductReviewRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductReviewRepository(new \Modules\Product\Entities\ProductReview());
        });
        $this->app->bind('Modules\Product\Repositories\ProductReviewImageRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductReviewImageRepository(new \Modules\Product\Entities\ProductReviewImage());
        });
        $this->app->bind('Modules\Product\Repositories\ProductReviewLikeRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductReviewLikeRepository(new \Modules\Product\Entities\ProductReviewLike());
        });
        $this->app->bind('Modules\Product\Repositories\ProductReviewCommentRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductReviewCommentRepository(new \Modules\Product\Entities\ProductReviewComment());
        });
        $this->app->bind('Modules\Product\Repositories\ProductDiscountRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductDiscountRepository(new \Modules\Product\Entities\ProductDiscount());
        });
        $this->app->bind('Modules\Product\Repositories\ProductImageRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductImageRepository(new \Modules\Product\Entities\ProductImage());
        });
        $this->app->bind('Modules\Product\Repositories\ProductRelatedRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductRelatedRepository(new \Modules\Product\Entities\ProductRelated());
        });
        $this->app->bind('Modules\Product\Repositories\ProductIncomboRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductIncomboRepository(new \Modules\Product\Entities\ProductIncombo());
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
        $this->app->bind('Modules\Product\Repositories\ProductFaqRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductFaqRepository(new \Modules\Product\Entities\ProductFaq());
        });
        $this->app->bind('Modules\Product\Repositories\ProductFaqDescRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductFaqDescRepository(new \Modules\Product\Entities\ProductFaqDesc());
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
        $this->app->bind('Modules\Product\Repositories\GiftSetRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentGiftSetRepository(new \Modules\Product\Entities\GiftSet());
        });
        $this->app->bind('Modules\Product\Repositories\GiftSetProductRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentGiftSetProductRepository(new \Modules\Product\Entities\GiftSetProduct());
        });
        $this->app->bind('Modules\Product\Repositories\GiftOrderRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentGiftOrderRepository(new \Modules\Product\Entities\GiftOrder());
        });
        $this->app->bind('Modules\Product\Repositories\GiftOrderHistoryRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentGiftOrderHistoryRepository(new \Modules\Product\Entities\GiftOrderHistory());
        });
        $this->app->bind('Modules\Product\Repositories\GiftOrderProductRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentGiftOrderProductRepository(new \Modules\Product\Entities\GiftOrderProduct());
        });
        $this->app->bind('Modules\Product\Repositories\FlashsaleRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentFlashsaleRepository(new \Modules\Product\Entities\Flashsale());
        });
        // Will remove
        $this->app->bind('Modules\Product\Repositories\ProductModuleRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductModuleRepository(new \Modules\Product\Entities\ProductModule());
        });
        $this->app->bind('Modules\Product\Repositories\ProductModuleDescRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentProductModuleDescRepository(new \Modules\Product\Entities\ProductModuleDesc());
        });
        $this->app->bind('Modules\Product\Repositories\OrderRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentOrderRepository(new \Modules\Product\Entities\Order());
        });
        $this->app->bind('Modules\Product\Repositories\OrderProductRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentOrderProductRepository(new \Modules\Product\Entities\OrderProduct());
        });
        $this->app->bind('Modules\Product\Repositories\OrderTotalRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentOrderTotalRepository(new \Modules\Product\Entities\OrderTotal());
        });
        $this->app->bind('Modules\Product\Repositories\OrderHistoryRepository', function() {
            return new \Modules\Product\Repositories\Eloquent\EloquentOrderHistoryRepository(new \Modules\Product\Entities\OrderHistory());
        });
        // End will remove
    }
}
