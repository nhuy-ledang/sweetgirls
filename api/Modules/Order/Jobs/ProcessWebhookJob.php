<?php namespace Modules\Order\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;

class ProcessWebhookJob implements ShouldQueue {
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
    public function __construct($data) {
        //$this->adapter = $adapter;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        //sleep(30);
        $response = Http::post('https://service.goldenperiod.vn/api/v2/webhooks/tedfast/receiveVtpOrders', $this->data);

        // Kiểm tra và xử lý phản hồi từ API
        if ($response->successful()) {
            // Thành công: Cập nhật trạng thái trong cơ sở dữ liệu
        } else {
            // Lỗi API: Ghi log hoặc thử lại
        }
    }
}
