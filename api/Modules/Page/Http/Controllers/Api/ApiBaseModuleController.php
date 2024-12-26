<?php namespace Modules\Page\Http\Controllers\Api;

use Modules\Core\Http\Controllers\Api\ApiBackendController;
use Modules\Page\Traits\ModuleRequestsTrait;
use Modules\Page\Traits\ModuleResultModelTrait;

abstract class ApiBaseModuleController extends ApiBackendController {
    use ModuleRequestsTrait, ModuleResultModelTrait;
    public $module_name = "page";
    public $module_prefix = "pg_";
}
