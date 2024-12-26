<?php namespace Modules\Usr\Http\Controllers;

abstract class ApiBaseController extends \Modules\Core\Http\Controllers\Api\ApiBackendController {
    protected function getDeviceInfo(&$input = []) {
        $input['ip'] = $this->request->server->get('REMOTE_ADDR');
        $name = $this->request->get('name');
        if ($name) $input['name'] = $name;
        $model = $this->request->get('model');
        if ($model) $input['model'] = $model;
        $device_platform = detect_platform();
        if ($device_platform) $input['device_platform'] = $device_platform;
        $device_token = detect_token();
        if ($device_token) $input['device_token'] = $device_token;

        return $input;
    }
}
