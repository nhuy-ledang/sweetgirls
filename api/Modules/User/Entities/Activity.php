<?php namespace Modules\User\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class Activity extends CoreModel {
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = 'user__activities';

    /**
     * Indicates if the model should be timestamped.
     * @var bool
     */
    public $timestamps = true;

    protected $fillable = ['user_id', 'key', 'comment', 'ip', 'payload'];

    /**
     * The attributes that should be hidden for serialization.
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be casted to native types.
     * @var array
     */
    protected $casts = [
        'id'      => 'integer',
        'user_id' => 'integer',
    ];

    public function setPayloadAttribute($value) {
        if (is_array($value)) {
            $this->attributes['payload'] = json_encode($value, true);
        } else {
            $this->attributes['payload'] = $value;
        }
    }

    public function getPayloadAttribute($value) {
        $output = [];
        try {
            $output = json_decode($value, true);
        } catch (\Exception $ex) {

        }
        return $output;
    }

    public function user() {
        return $this->belongsTo('Modules\User\Entities\Sentinel\User');
    }

    /**
     * Logger
     * @param $user_id
     * @param $key
     * @param $comment
     * @param $payload
     * @param $ip
     */
    public static function logger($user_id, $key, $comment, $payload = null, $ip) {
        $input = [
            'user_id' => $user_id,
            'key'     => $key,
            'comment' => $comment,
        ];
        if ($payload) $input['payload'] = $payload;
        if ($ip) $input['ip'] = $ip;

        self::create($input);
    }
}
