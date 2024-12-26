<?php namespace Modules\Business\Http\Controllers\Api;

use Modules\Core\Http\Controllers\Api\ApiBackendController;

/**
 * Class ApiBaseModuleController
 *
 * @package Modules\Business\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2023-04-05
 */
abstract class ApiBaseModuleController extends ApiBackendController {
    public $module_name = 'business';
    public $module_prefix = 'bus_';

    /**
     * Check is View Own
     * @param $module
     * @return bool
     */
    protected function isViewOwn($module = 'business') {
        return $this->isCRUD($module, 'view_own');
    }

    /**
     * Check is View
     * @param $module
     * @return bool
     */
    protected function isView($module = 'business') {
        return $this->isCRUD($module, 'view');
    }

    /**
     * Check is Create
     * @param $module
     * @return bool
     */
    protected function isCreate($module = 'business') {
        return $this->isCRUD($module, 'create');
    }

    /**
     * Check is Update
     * @param $module
     * @return bool
     */
    protected function isUpdate($module = 'business') {
        return $this->isCRUD($module, 'edit');
    }

    /**
     * Check is Delete
     * @param $module
     * @return bool
     */
    protected function isDelete($module = 'business') {
        return $this->isCRUD($module, 'delete');
    }
}
