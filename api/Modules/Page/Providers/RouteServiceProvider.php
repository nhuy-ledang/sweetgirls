<?php namespace Modules\Page\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider {
    /**
     * The module namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $moduleNamespace = 'Modules\Page\Http\Controllers';

    /**
     * Called before routes are registered.
     * Register any model bindings or pattern based filters.
     *
     * @return void
     */
    public function boot() {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map() {
        $this->mapApiRoutes();
        $this->mapApiBackendRoutes();
        //$this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes() {
        Route::middleware('web')
            ->namespace($this->moduleNamespace)
            ->group(module_path('Page', '/Routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes() {
        Route::middleware('api')
            //->prefix('api')
            ->namespace($this->moduleNamespace . '\ApiPublic')
            ->group(module_path('Page', '/Routes/apiPublic.php'));
    }

    /**
     * Define the "api backend" routes for the application.
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiBackendRoutes() {
        Route::middleware('api')
            ->prefix('backend')
            ->namespace($this->moduleNamespace . '\Api')
            ->group(module_path('Page', '/Routes/api.php'));
    }
}
