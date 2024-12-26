<?php namespace Modules\Order\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OrderWheelJob implements ShouldQueue {
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
        $order = $data['model'];
        $email = $order->email;
        $data['config_owner'] = isset($data['setting']['config_owner']) ? $data['setting']['config_owner'] : '';
        // Send to customer
        if (!empty($email)) $this->adapter->send('order::mail/order_wheel', $data, function($message) use ($email, $order) {
            $message->to($email)->subject("Bạn đã nhận 1 lượt quay Vòng quay may mắn");
        });
        // Send alert to admin
        /*if (!empty($data['emails'])) {
            $emails = $data['emails'];
            $this->adapter->send('product::mail/order_add_alert', $data, function($message) use ($emails) {
                foreach ($emails as $email) $message->to($email)->subject('Khách hàng đặt hàng');
            });
        }*/
    }
}
