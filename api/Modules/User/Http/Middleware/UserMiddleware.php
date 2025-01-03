<?php namespace Modules\User\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Lang;
use Modules\User\Repositories\Authentication;
use Modules\Core\Helper\ErrorFormat;
use Sentinel;

/***
 * Class UserMiddleware
 *
 * @package Modules\Platform\Http\Middleware

 */
class UserMiddleware {
    /**
     * The Authentication implementation.
     *
     * @var Authentication
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param Authentication $auth
     */
    public function __construct(Authentication $auth) {
        $this->auth = $auth;
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
        $access_token = requestValue('Authorization');
        //if (!$access_token) $access_token = requestValue('access_token');
        if (!$access_token) return response(['data' => null, 'errors' => [new ErrorFormat($errorcodes['system.token_required'])]], 401);
        $user = $this->auth->findByPersistenceCode($access_token);
        if (!$user) {
            //=== User has deleted
            if ($this->auth->isCodeValid($access_token)) {
                return response(['error' => true, 'data' => null, 'errors' => [new ErrorFormat($errorcodes['system.suspended'])]], 401);
            } else {
                //=== Remove session
                $this->auth->logout();
                return response(['error' => true, 'data' => null, 'errors' => [new ErrorFormat($errorcodes['system.unauthorized'])]], 401);
            }
        }
        //if ($this->auth->isExpires($access_token)) return response(['error' => true, 'data' => null, 'errors' => [new ErrorFormat($errorcodes['access_denied.expired'])]], 401);
        if ($user->status != USER_STATUS_ACTIVATED) {
            $errorKey = 'auth.not_activated';
            if ($user->status == USER_STATUS_BANNED) $errorKey = 'auth.banned';
            return response(['error' => true, 'data' => null, 'errors' => [new ErrorFormat($errorcodes[$errorKey])]], 401);
        }
        //if ($user->password_failed >= USER_PASSWORD_FAILED_MAX) return response(['error' => true, 'data' => null, 'errors' => [new ErrorFormat($errorcodes['auth.locked'])]], 401);
        $this->auth->setUser($user);

        return $next($request);
    }
}
