<?php

namespace Modules\Notify\Services\MobileNotification;

use Modules\Notify\Services\BaseMessage;

class Message extends BaseMessage {
    protected $from;
    protected $to;
    protected $title;
    protected $content;
    protected $send_time;
    protected $target_segment;
    protected $target_devices;
    protected $custom_data;
    protected $response_data;
    /**
     * @var
     */
    protected $status;
    protected $user_ids;

    /**
     * @return mixed
     */
    public function getStatus() {
        return $this->status;
    }


    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResponseData() {
        return $this->response_data;
    }

    /**
     * @param mixed $response_data
     * @return $this
     */
    public function setResponseData($response_data) {
        $this->response_data = $response_data;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCustomData() {
        return $this->custom_data;
    }

    /**
     * @param mixed $custom_data
     * @return $this
     */
    public function setCustomData($custom_data) {
        $this->custom_data = $custom_data;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFrom() {
        return $this->from;
    }

    /**
     * @param mixed $from
     * @return $this
     */
    public function setFrom($from) {
        $this->from = $from;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTo() {
        if (is_null($this->to)) {
            $target_devices = $this->getTargetDevices();
            $this->to = $target_devices;//array_merge(array_values($target_devices));
        }
        return $this->to;
    }

    /**
     * @param mixed $to
     * @return $this
     */
    public function setTo($to) {
        $this->to = $to;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle() {
        //        if(is_null($this->title)){
        //            $this->title = $this->getContent();
        //        }
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return $this
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @param mixed $content
     * @return $this
     */
    public function setContent($content) {
        $this->content = $content;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSendTime() {
        return $this->send_time;
    }

    /**
     * @param mixed $send_time
     * @return $this
     */
    public function setSendTime($send_time) {
        $this->send_time = $send_time;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTargetSegment() {
        return $this->target_segment;
    }

    /**
     * @param mixed $target_segment
     * @return $this
     */
    public function setTargetSegment($target_segment) {
        $this->target_segment = $target_segment;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTargetDevices() {
        return $this->target_devices;
    }

    /**
     * @param mixed $target_devices
     * @return $this
     */
    public function setTargetDevices($target_devices) {
        $this->target_devices = $target_devices;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserIds() {
        return $this->user_ids;
    }

    /**
     * @param mixed $user_ids
     * @return $this
     */
    public function setUserIds($user_ids) {
        $this->user_ids = $user_ids;

        return $this;
    }
}
