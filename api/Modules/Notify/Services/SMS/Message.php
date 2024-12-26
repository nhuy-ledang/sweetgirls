<?php

namespace Modules\Notify\Services\SMS;


use Modules\Notify\Services\BaseMessage;

class Message extends BaseMessage {
    protected $from;
    protected $title;
    protected $to;
    protected $content;
    protected $response_data;
    protected $status;

    /**
     * @return mixed
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status) {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getResponseData() {
        return $this->response_data;
    }

    /**
     * @param mixed $response_data
     */
    public function setResponseData($response_data) {
        $this->response_data = $response_data;
    }

    /**
     * @return mixed
     */
    public function getFrom() {
        return $this->getSrc();
    }

    //    /**
    //     * @param mixed $from
    //     */
    //    public function setFrom($from)
    //    {
    //        $this->from = $from;
    //    }

    /**
     * @return mixed
     */
    public function getTitle() {
        return $this->getSrc();
    }

    //    /**
    //     * @param mixed $title
    //     */
    //    public function setTitle($title)
    //    {
    //        $this->title = $title;
    //    }

    /**
     * @return mixed
     */
    public function getTo() {
        $this->to = $this->getDst();
        if (is_string($this->to)) {
            $this->to = explode('<', $this->to);
        }
        return $this->to;
    }

    //    /**
    //     * @param mixed $to
    //     */
    //    public function setTo($to)
    //    {
    //        $this->to = $to;
    //    }

    /**
     * @return mixed
     */
    public function getContent() {
        return $this->getText();
    }

    //    /**
    //     * @param mixed $content
    //     */
    //    public function setContent($content)
    //    {
    //        $this->content = $content;
    //    }

    protected $src;
    protected $dst;
    protected $text;
    protected $type = 'sms';

    /**
     * @return mixed
     */
    public function getSrc() {
        return $this->src;
    }

    /**
     * @param mixed $src
     */
    public function setSrc($src) {
        $this->src = $src;
    }

    /**
     * @return mixed
     */
    public function getDst() {
        if (!is_string($this->dst)) {
            $this->dst = implode("<", (array)$this->dst);
        }
        return $this->dst;
    }

    /**
     * @param mixed $dst
     */
    public function setDst($dst) {
        $this->dst = $dst;
    }

    /**
     * @return mixed
     */
    public function getText() {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text) {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type) {
        $this->type = $type;
    }
}