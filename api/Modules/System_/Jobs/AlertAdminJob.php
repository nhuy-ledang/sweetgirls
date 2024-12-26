<?php namespace Modules\System\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AlertAdminJob implements ShouldQueue {
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
     * @var $emails
     */
    protected $emails;

    /**
     * @var $data
     */
    protected $data = [];

    /**
     * Create a new job instance.
     * @param $adapter
     * @param array $emails
     * @param array $data
     */
    public function __construct($adapter, $emails, $data = []) {
        $this->adapter = $adapter;
        $this->emails = $emails;
        $this->data = $data;
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle() {
        //sleep(30);
        $emails = $this->emails;
        $obj = array_merge(['subject' => 'Cảnh báo', 'message' => 'Cảnh báo'], $this->data);
        $this->adapter->send('system::alertAdmin', ['obj' => $obj], function($message) use ($emails, $obj) {
            foreach ($emails as $email) {
                $message->to($email)->subject($obj['subject']);
            }
        });
    }
}
