<?php namespace Modules\Core\Http\Controllers\Api;

use Illuminate\Http\Request;

/**
 * Class ApiBackendController
 *
 * @package Modules\Core\Http\Controllers\Api
 */
abstract class ApiBackendController extends ApiBaseController {
    /**
     * @var \Modules\Usr\Repositories\Authentication
     */
    protected $_auth;

    /**
     * @var \Modules\Usr\Entities\Sentinel\User
     */
    protected $auth;

    /**
     * @param Request $request
     */
    public function __construct(Request $request) {
        $this->_auth = app('Modules\Usr\Repositories\Authentication');
        $this->middleware(function($request, $next) {
            if ($request->access_token) $this->access_token = $request->access_token;
            $this->auth();
            return $next($request);
        });
        parent::__construct($request);
    }

    /**
     * Check is cms
     *
     * @return bool
     */
    protected function isCms() {
        return detect_env() == 'cms';
    }

    /**
     * Check auth
     *
     * @return false|\Modules\Usr\Entities\Sentinel\User
     */
    protected function auth() {
        return $this->auth = $this->_auth->getUser();
    }

    /**
     * Check login -- UserMiddleware
     *
     * @return false|\Modules\User\Entities\Sentinel\User
     */
    protected function isLogged() {
        $user = $this->auth();
        if ($user) {
            return $user;
        } else {
            $access_token = requestValue('sAuthorization');
            if (!$access_token) return false;
            $user = $this->_auth->findByPersistenceCode($access_token);
            if (!$user) return false;
            $this->_auth->setUser($user);
            $this->access_token = $access_token;

            return $this->auth();
        }
    }

    /**
     * Check is Super Admin
     *
     * @return bool
     */
    protected function isSuperAdmin() {
        return $this->_auth->isSuperAdmin($this->auth);
    }

    /**
     * Check is Supper or Admin
     *
     * @return bool
     */
    protected function isAdmin() {
        return $this->_auth->isAdmin($this->auth);
    }

    /**
     * Check is Manager
     *
     * @return bool
     */
    protected function isManager() {
        return $this->_auth->isManager($this->auth);
    }

    /**
     * Check is Accountant
     *
     * @return bool
     */
    protected function isAccountant() {
        return $this->_auth->isAccountant($this->auth);
    }

    /**
     * Check is Sales
     *
     * @return bool
     */
    protected function isSales() {
        return $this->_auth->isSales($this->auth);
    }

    /**
     * Check is User
     *
     * @return bool
     */
    protected function isUser() {
        return $this->_auth->isUser($this->auth);
    }

    /**
     * Check is Access Admin
     *
     * @return bool
     */
    protected function isAccessAdmin() {
        return $this->isAdmin() || $this->isManager() || $this->isAccountant() || $this->isSales() || $this->isUser();
    }

    /***
     * Check is CRUD
     * @param string $module
     * @param string $crud : view_own,view,create,edit,delete
     * @return bool
     */
    protected function isCRUD($module = 'projects', $crud = 'view') {
        if (!$this->auth) return false;
        if ($this->isSuperAdmin()) return true;
        $permissions = $this->auth->permissions;
        $isAllow = $permissions && !empty($permissions[$module]) && in_array($crud, $permissions[$module]);
        if (!$isAllow) {
            foreach ($this->auth->roles as $role) {
                $permissions = $role->permissions ? $role->permissions : [];
                $isAllow = $permissions && !empty($permissions[$module]) && in_array($crud, $permissions[$module]);
                if ($isAllow) break;
            }
        }
        //var_dump($permissions); exit();

        return $isAllow;
    }
}
