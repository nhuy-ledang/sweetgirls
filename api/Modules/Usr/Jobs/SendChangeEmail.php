<?php namespace Modules\Usr\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendChangeEmail implements ShouldQueue {
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
     * @var $user
     */
    protected $user;

    /**
     * @var $data
     */
    protected $data;

    /**
     * Create a new job instance.
     * @param $adapter
     * @param $user
     * @param $data
     * @return void
     */
    public function __construct($adapter, $user, $data) {
        $this->adapter = $adapter;
        $this->user = $user;
        $this->data = $data;
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle() {
        //sleep(30);
        $model = $this->user;
        $this->adapter->send('usr::mail.changeEmail', $this->data, function($message) use ($model) {
            $message->to($model->email)->subject('Change Email');
        });
    }
}
