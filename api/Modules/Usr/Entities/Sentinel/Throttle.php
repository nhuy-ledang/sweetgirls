<?php namespace Modules\Usr\Entities\Sentinel;

use Modules\Usr\Sentinel\EloquentThrottle;

class Throttle extends EloquentThrottle {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usr__throttle';
}
