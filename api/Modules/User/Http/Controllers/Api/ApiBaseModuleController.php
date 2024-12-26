<?php namespace Modules\User\Http\Controllers\Api;

use Carbon\Carbon;
use Modules\Core\Http\Controllers\Api\ApiBackendController;

/**
 * Class ApiBaseModuleController
 * @package Modules\User\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 7/19/2018 11:03 PM
 */
abstract class ApiBaseModuleController extends ApiBackendController {
    public $module_name = "user";
}
