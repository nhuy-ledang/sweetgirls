<?php namespace Modules\Business\Http\Controllers\ApiPublic;

use Modules\Core\Http\Controllers\Api\ApiPublicController;

abstract class ApiBaseModuleController extends ApiPublicController {
    public $module_name = 'business';
    public $module_prefix = 'bus_';
}
