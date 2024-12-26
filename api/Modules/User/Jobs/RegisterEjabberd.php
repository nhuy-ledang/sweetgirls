<?php namespace Modules\User\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\User\Entities\Sentinel\User;

class RegisterEjabberd implements ShouldQueue {
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
    public $maxExceptions = 100;

    /**
     * @var $user_id
     */
    protected $user_id;

    /**
     * @var $request_id
     */
    protected $request_id;

    /**
     * Create a new job instance.
     * @param $user_id
     * @param $request_id
     * @return void
     */
    public function __construct($user_id, $request_id = false) {
        $this->user_id = $user_id;
        $this->request_id = $request_id;
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle() {
//        $url = env('EJABBERD_RESTFUL', 'http://onllearn.vn:5280/api') . '/register';
//        $data = [
//            'user'     => $this->user_id . ($this->request_id ? '_' . $this->request_id : ''),
//            'host'     => env('EJABBERD_HOST', 'onllearn.vn'),
//            'password' => env('EJABBERD_USER_PASSWORD', '123456')
//        ];
//        $user = env('EJABBERD_ADMIN_ACCOUNT', '') . ':' . env('EJABBERD_ADMIN_PASSWORD', '');
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
//        curl_setopt($ch, CURLOPT_USERPWD, $user);
//        curl_setopt($ch, CURLOPT_POST, 1);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        $response = curl_exec($ch);
//        curl_close($ch);
//        $result = json_decode($response, true);
//        if (!$this->request_id) {
//            if (is_string($result) || (is_array($result) && isset($result['code']) && $result['code'] == 10090)) {
//                User::where('id', $this->user_id)->update(['reg_chat' => true]);
//            }
//        }
//        return $result;
    }
}
