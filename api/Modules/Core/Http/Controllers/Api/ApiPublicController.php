<?php namespace Modules\Core\Http\Controllers\Api;

use Illuminate\Http\Request;

/**
 * Class ApiPublicController
 *
 * @package Modules\Core\Http\Controllers\Api
 */
abstract class ApiPublicController extends ApiBaseController {
    /**
     * @var \Modules\User\Repositories\Authentication
     */
    protected $_auth;

    /**
     * @var \Modules\User\Entities\Sentinel\User
     */
    protected $auth;

    /**
     * @param Request $request
     */
    public function __construct(Request $request) {
        $this->_auth = app('Modules\User\Repositories\Authentication');
        $this->middleware(function($request, $next) {
            if ($request->access_token) $this->access_token = $request->access_token;
            $this->auth();
            return $next($request);
        });
        parent::__construct($request);
    }

    /**
     * Check auth
     *
     * @return \Cartalyst\Sentinel\Users\UserInterface
     */
    protected function auth() {
        return $this->auth = $this->_auth->getUser();
    }

    /**
     * Check login -- UserMiddleware
     *
     * @return bool|\Cartalyst\Sentinel\Users\UserInterface
     */
    protected function isLogged() {
        $user = $this->auth();
        if ($user) {
            return $user;
        } else {
            $access_token = requestValue('Authorization');
            if (!$access_token) return false;
            $user = $this->_auth->findByPersistenceCode($access_token);
            if (!$user) return false;
            $this->_auth->setUser($user);
            $this->access_token = $access_token;

            return $this->auth();
        }
    }
}
