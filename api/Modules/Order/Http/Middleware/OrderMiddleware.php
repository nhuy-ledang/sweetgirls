<?php namespace Modules\Order\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Lang;
use Modules\Core\Helper\ErrorFormat;

/***
 * Class OrderMiddleware
 *
 * @package Modules\Order\Http\Middleware
 * @author Huy D <huydang1920@gmail.com>
 */
class OrderMiddleware {
    /**
     * Create a new filter instance.
     */
    public function __construct() {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $errorcodes = Lang::get('errorcodes');
        $secret_key = $request->get('secret_key');
        $env = env('APP_SECRET', 'secret_key');
        if ($env && $env != 'secret_key' && $env != $secret_key) {
            return response(['data' => null, 'errors' => [new ErrorFormat($errorcodes['system.token_required'])]], 401);
        }

        return $next($request);
    }
}
