<?php namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

/***
 * Class LocaleMiddleware
 *
 * @package Modules\Core\Http\Middleware

 */
class LocaleMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $locale = $request->get('locale');
        if (!$locale) {
            $locale = 'vi';
        }
        if (!in_array($locale, ['vi', 'en'])) {
            $locale = 'vi';
        }

        // Set Current Locale
        if ($request->getLocale() != $locale) {
            $request->setLocale($locale);
            App::setLocale($locale);
        }

        return $next($request);
    }
}