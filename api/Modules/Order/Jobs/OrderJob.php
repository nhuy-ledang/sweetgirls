<?php namespace Modules\Order\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OrderJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * The maximum number of exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 10;

    /**
     * @var $adapter
     */
    protected $adapter;

    /**
     * @var $data
     */
    protected $data;

    /**
     * Create a new job instance.
     *
     * @param $adapter
     * @param $data
     * @return void
     */
    public function __construct($adapter, $data) {
        $this->adapter = $adapter;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        //sleep(30);
        $emails = $this->data['emails'];
        $attached = $this->data['attached'];
        $attach_file = $this->data['attach_file'];
        $data = $this->data['data'];
        $cus_emails = $emails[0];
        if ($cus_emails) {
            $this->adapter->send('order::mail/order', $data, function($message) use ($cus_emails, $attached, $attach_file) {
                $message->subject('Biên lai');
                $message->to($cus_emails[0]);
                array_shift($cus_emails);
                if ($cus_emails) $message->bcc($cus_emails);
                if ($attached) $message->attach($attach_file, ['as' => 'Bien-lai.pdf']);
            });
        }
        // Alert to admin
        $admin_emails = isset($emails[1]) ? $emails[1] : [];
        if ($admin_emails) {

        }
    }
}
