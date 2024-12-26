<?php namespace Modules\Page\Http\Controllers\ApiPublic;

use Modules\Core\Http\Controllers\Api\ApiPublicController;

abstract class ApiBaseModuleController extends ApiPublicController {
    public $module_name = "page";
    public $module_prefix = "pg_";
}
