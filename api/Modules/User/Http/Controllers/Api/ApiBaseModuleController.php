<?php namespace Modules\User\Http\Controllers\Api;

use Carbon\Carbon;
use Modules\Core\Http\Controllers\Api\ApiBackendController;

/**
 * Class ApiBaseModuleController
 * @package Modules\User\Http\Controllers\Api
 */
abstract class ApiBaseModuleController extends ApiBackendController {
    public $module_name = "user";
}
