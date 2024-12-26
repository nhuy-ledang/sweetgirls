<?php namespace Modules\Notify\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

/**
 * Class MailController
 * @package Modules\Notify\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 9/10/2018 4:37 PM
 * @SWG\Resource(
 *   apiVersion="1.0.0",
 *   swaggerVersion="1.2",
 *   resourcePath="/NotifyMail",
 *   description="Notify Mail Api"
 * )
 */
class MailController extends ApiBaseModuleController {
    protected $maximumLimit = 20;

    public function __construct(Request $request) {
        $this->middleware('auth.usr');

        parent::__construct($request);
    }

    /**
     * Get the validation rules for create.
     * @return array
     */
    protected function rulesForCreate() {
        return [];
    }

    /**
     * Get the validation rules for update.
     * @param int $id
     * @return array
     */
    protected function rulesForUpdate($id) {
        return [];
    }

    /**
     * @SWG\Model(id="NotifyMailModel")
     * @SWG\Property(name="content", type="string", required=true),
     * @SWG\Api(
     *   path="/backend/notify/mail",
     *   @SWG\Operation(
     *      method="POST",
     *      summary="Create Notify Mail",
     *      nickname="createNotifyMail",
     *      @SWG\Parameter(name="body", description="Request body", required=true, type="NotifyMailModel", paramType="body", allowMultiple=false),
     *      @SWG\ResponseMessage(code=200, message="OK"),
     *      @SWG\ResponseMessage(code=400, message="Invalid request params"),
     *      @SWG\ResponseMessage(code=401, message="Caller is not authenticated"),
     *      @SWG\ResponseMessage(code=404, message="Resource not found")
     *   )
     * )
     */
    public function store() {
        try {
            $content = (string)$this->request->get('content');

            $data = [];
            if ($content) {
                $arr = preg_split("/\r\n|\n|\r/", $content);
                foreach ($arr as $it) {
                    if (trim($it)) {
                        $d = explode('|', $it);
                        if (count($d) == 3) {
                            $fullname = trim($d[0]);
                            $email = trim($d[1]);
                            $content = trim($d[2]);
                            if ($fullname && $email) {
                                $data[] = ['fullname' => utf8_strtoupper($fullname), 'content' => utf8_strtoupper($content), 'email' => $email];
                            }
                        }
                    }
                }
            }

            foreach ($data as $item) {
                $fullname = $item['fullname'];
                $email = $item['email'];
                $this->email->send('notify::mail', $item, function ($message) use ($email) {
                    $message->to($email)->subject('Motila Corp - Truyền thông kỹ thuật toàn diện nhất');
                });
            }

            return $this->respondWithSuccess($data);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @SWG\Api(
     *   path="/backend/notify/mail/test",
     *   @SWG\Operation(
     *      method="POST",
     *      summary="Create Notify Mail Test",
     *      nickname="createNotifyMailTest",
     *      @SWG\Parameter(name="body", description="Request body", required=true, paramType="body", allowMultiple=false),
     *      @SWG\ResponseMessage(code=200, message="OK"),
     *      @SWG\ResponseMessage(code=400, message="Invalid request params"),
     *      @SWG\ResponseMessage(code=401, message="Caller is not authenticated"),
     *      @SWG\ResponseMessage(code=404, message="Resource not found")
     *   )
     * )
     */
    public function test() {
        try {
            $first_name = '';
            $last_name = '';
            $email = 'huydang1920@gmail.com';
            /*Mail::queue('notify::mail', compact('first_name', 'last_name'), function (Message $m) use ($email) {
                $m->to($email)->subject('Gởi email');
            });*/

            $this->email->send('notify::mail', compact('first_name', 'last_name'), function ($message) use ($email) {
                $message->to($email)->subject('Gởi email');
            });

            return $this->respondWithSuccess([]);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
