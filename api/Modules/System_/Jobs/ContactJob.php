<?php namespace Modules\System\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ContactJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     * @var int
     */
    public $tries = 1;

    /**
     * The maximum number of exceptions to allow before failing.
     * @var int
     */
    public $maxExceptions = 1;

    /**
     * @var $adapter
     */
    protected $adapter;

    /**
     * @var \Modules\System\Entities\Contact
     */
    protected $model;

    /**
     * @var $emails
     */
    protected $emails;

    /**
     * Create a new job instance.
     * @param $adapter
     * @param \Modules\System\Entities\Contact $model
     * @param [] $emails
     * @return void
     */
    public function __construct($adapter, $model, $emails) {
        $this->adapter = $adapter;
        $this->model = $model;
        $this->emails = $emails;
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle() {
        //sleep(30);
        $emails = $this->emails;
        $this->adapter->send('system::contact', ['obj' => $this->model], function($message) use ($emails) {
            //$message->from(env('MAIL_FROM_ADDRESS', 'motila@motila.vn'), 'KHÁCH HÀNG CẦN TƯ VẤN');
            foreach ($emails as $email) {
                $message->to($email)->subject('Khách hàng cần tư vấn');
            }
        });
    }
}
