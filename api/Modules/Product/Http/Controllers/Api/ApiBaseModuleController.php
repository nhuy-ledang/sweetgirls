<?php namespace Modules\Product\Http\Controllers\Api;

use Modules\Core\Http\Controllers\Api\ApiBackendController;

abstract class ApiBaseModuleController extends ApiBackendController {
    public $module_name = "product";
    public $module_prefix = "pd_";
}
