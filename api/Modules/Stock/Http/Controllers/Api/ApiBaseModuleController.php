<?php namespace Modules\Stock\Http\Controllers\Api;

use Modules\Core\Http\Controllers\Api\ApiBackendController;

/**
 * Class ApiBaseModuleController
 *
 * @package Modules\Stock\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2022-07-22
 */
abstract class ApiBaseModuleController extends ApiBackendController {
    public $module_name = 'stock';
    public $module_prefix = 'sto_';

    /**
     * Check is View Own
     * @param $module
     * @return bool
     */
    protected function isViewOwn($module = 'stocks') {
        return $this->isCRUD($module, 'view_own');
    }

    /**
     * Check is View
     * @param $module
     * @return bool
     */
    protected function isView($module = 'stocks') {
        return $this->isCRUD($module, 'view');
    }

    /**
     * Check is Create
     * @param $module
     * @return bool
     */
    protected function isCreate($module = 'stocks') {
        return $this->isCRUD($module, 'create');
    }

    /**
     * Check is Update
     * @param $module
     * @return bool
     */
    protected function isUpdate($module = 'stocks') {
        return $this->isCRUD($module, 'edit');
    }

    /**
     * Check is Delete
     * @param $module
     * @return bool
     */
    protected function isDelete($module = 'stocks') {
        return $this->isCRUD($module, 'delete');
    }
}
