<?php namespace Modules\Product\Http\Controllers\ApiPublic;

use Modules\Core\Http\Controllers\Api\ApiPublicController;

abstract class ApiBaseModuleController extends ApiPublicController {
    public $module_name = "product";
    public $module_prefix = "pd_";
}
