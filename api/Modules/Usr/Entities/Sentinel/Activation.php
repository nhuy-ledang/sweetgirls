<?php namespace Modules\Usr\Entities\Sentinel;

use Modules\Usr\Sentinel\EloquentActivation;

class Activation extends EloquentActivation {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usr__activations';
}
