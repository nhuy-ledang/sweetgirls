<?php namespace Modules\Order\Http\Controllers\ApiPublic;

use Modules\Core\Http\Controllers\Api\ApiPublicController;

abstract class ApiBaseModuleController extends ApiPublicController {
    public $module_name = "order";
    public $module_prefix = "ord_";
}
