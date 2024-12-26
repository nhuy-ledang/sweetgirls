<?php namespace Modules\Usr\Http\Controllers\Api;

use Modules\Usr\Http\Controllers\ApiBaseController;

abstract class ApiBaseModuleController extends ApiBaseController {
    public $module_name = "usr";
    public $module_prefix = "usr_";

    /**
     * Check is View Own
     * @param $module
     * @return bool
     */
    protected function isViewOwn($module = 'usrs') {
        return $this->isCRUD($module, 'view_own');
    }

    /**
     * Check is View
     * @param $module
     * @return bool
     */
    protected function isView($module = 'usrs') {
        return $this->isCRUD($module, 'view');
    }

    /**
     * Check is Create
     * @param $module
     * @return bool
     */
    protected function isCreate($module = 'usrs') {
        return $this->isCRUD($module, 'create');
    }

    /**
     * Check is Update
     * @param $module
     * @return bool
     */
    protected function isUpdate($module = 'usrs') {
        return $this->isCRUD($module, 'edit');
    }

    /**
     * Check is Delete
     * @param $module
     * @return bool
     */
    protected function isDelete($module = 'usrs') {
        return $this->isCRUD($module, 'delete');
    }
}
