<?php namespace Modules\System\Http\Controllers\Api;

use Modules\Core\Http\Controllers\Api\ApiBackendController;

abstract class ApiBaseModuleController extends ApiBackendController {
    public $module_name = "system";
}
