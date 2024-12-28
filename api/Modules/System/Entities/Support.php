<?php namespace Modules\System\Entities;

use Modules\Core\Entities\Eloquent\CoreModel;

class Support extends CoreModel {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contact__supports';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['parent_id', 'service_id', 'from', 'to', 'title', 'message', 'readed', 'replied', 'attaches'];

    /**
     * The attributes that should be casted to native types.
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
        'parent_id'  => 'integer',
        'service_id' => 'integer',
        'from'       => 'integer',
        'to'         => 'integer',
        'readed'     => 'boolean',
        'replied'    => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     * @var array
     */
    protected $appends = ['service', 'reply'];

    public function setAttachesAttribute($value) {
        if (is_array($value)) {
            $this->attributes['attaches'] = json_encode($value, true);
        } else {
            $this->attributes['attaches'] = $value;
        }
    }

    public function getAttachesAttribute($value) {
        $attaches = [];
        if ($value) {
            try {
                $results = json_decode($value, true);
                if ($results) {
                    foreach ($results as $path) {
                        $url = media_url_file($path);
                        $paths = explode('/', $url);
                        $attaches[] = [
                            'filename' => $paths[count($paths) - 1],
                            'url'      => $url
                        ];
                    }
                }
            } catch (\Exception $ex) {
            }
        }
        return $attaches;
    }

    public function getServiceAttribute() {
        if ($this->locale == 'en') {
            $list = ['N/A', 'Technical Support', 'Support for consulting new service registration', 'Support for online payment order issues', 'Support for renewal service consultation', 'Complaints - Suggestions (No technical problems can be solved immediately)'];
        } else {
            $list = ['N/A', 'Hỗ trợ kỹ thuật', 'Hỗ trợ tư vấn đăng ký mới dịch vụ', 'Hỗ trợ các vấn đề đơn hàng thanh toán online', 'Hỗ trợ tư vấn gia hạn dịch vụ', 'Than phiền - Góp ý (Không xử lý tức thời vấn đề kỹ thuật)'];
        }
        if (isset($list[$this->service_id])) {
            return $list[$this->service_id];
        } else {
            return $list[0];
        }
    }

    public function getReplyAttribute() {
        if ($this->replied) {
            return $this->locale == 'en' ? 'Answered' : 'Đã trả lời';
        } else {
            return $this->locale == 'en' ? 'Unanswered' : 'Chưa trả lời';
        }
    }

    public function getMessageAttribute($value) {
        return html_decode_helper($value);
    }

    /**
     * Relationship
     */
    public function fromUser() {
        return $this->belongsTo('\Modules\User\Entities\Sentinel\User', 'from', 'id')->select(['id', 'email', 'first_name', 'last_name', 'avatar']);
    }

    public function toUser() {
        return $this->belongsTo('\Modules\User\Entities\Sentinel\User', 'to', 'id')->select(['id', 'email', 'first_name', 'last_name', 'avatar']);
    }
}
