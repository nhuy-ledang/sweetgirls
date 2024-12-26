<?php namespace Modules\Order\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OrderAddJob implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 15;

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
        $data = $this->data;
        $model = $data['model'];
        // Send to customer
        if (!empty($model->email)) $this->adapter->send('order::mail/order_add', $data, function($message) use ($model, $data) {
            $message->to($model->email)->subject("{$data['config_owner']} xác nhận đơn hàng {$model->no}");
        });
        // Send alert to admin
        if (!empty($data['emails'])) {
            $emails = $data['emails'];
            $this->adapter->send('order::mail/order_add_alert', $data, function($message) use ($emails) {
                foreach ($emails as $email) $message->to($email)->subject('Khách hàng đặt hàng');
            });
        }
    }
}
