<?php namespace Modules\Usr\Entities\Sentinel;

use Modules\Usr\Sentinel\EloquentPersistence;

class Persistence extends EloquentPersistence {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usr__persistences';
}
