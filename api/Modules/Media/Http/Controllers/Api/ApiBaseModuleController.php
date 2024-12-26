<?php namespace Modules\Media\Http\Controllers\Api;

use Modules\Core\Http\Controllers\Api\ApiBackendController;

abstract class ApiBaseModuleController extends ApiBackendController {
    public $module_name = "media";
    public $module_prefix = "med_";
}
