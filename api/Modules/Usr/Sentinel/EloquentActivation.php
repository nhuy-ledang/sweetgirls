<?php namespace Modules\Usr\Sentinel;

use Cartalyst\Sentinel\Activations\ActivationInterface;
use Modules\Core\Entities\Eloquent\CoreModel;

//use Cartalyst\Sentinel\Activations\EloquentActivation;

// Refer: Cartalyst\Sentinel\Activations\EloquentActivation
class EloquentActivation extends CoreModel implements ActivationInterface {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'completed',
        'completed_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'completed' => 'bool',
    ];

    /**
     * {@inheritdoc}
     */
    public function getCode(): string {
        return $this->attributes['code'];
    }
}
