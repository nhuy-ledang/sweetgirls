<?php namespace Modules\User\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Lang;

class SendPasswordChanged implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     * @var int
     */
    public $tries = 15;

    /**
     * The maximum number of exceptions to allow before failing.
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
     * @return void
     */
    public function handle() {
        //sleep(30);
        $data = $this->data;
        $this->adapter->send('user::mail.passwordChanged', $data, function($message) use ($data) {
            $message->to($data['email'])->subject(Lang::get('messages.email_title.password_changed'));
        });
    }
}