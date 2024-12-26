<?php namespace Modules\Notify\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Notify\Repositories\ContactRepository;

/**
 * Class ContactController
 *
 * @package Modules\Notify\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 9/10/2018 4:37 PM
 * @SWG\Resource(
 *   apiVersion="1.0.0",
 *   swaggerVersion="1.2",
 *   resourcePath="/NotifyContacts",
 *   description="Notify Contacts Api"
 * )
 */
class ContactController extends ApiBaseModuleController {
    public function __construct(Request $request, ContactRepository $contact_repository) {
        $this->model_repository = $contact_repository;

        $this->middleware(\Illuminate\Session\Middleware\StartSession::class);

        parent::__construct($request);
    }

    /**
     * Get the validation rules for create.
     *
     * @return array
     */
    protected function rulesForCreate() {
        return [
            'fullname'  => 'required',
            'email'     => 'required|email',
            'content'   => 'required',
            'recaptcha' => 'required'
        ];
    }

    /**
     * @SWG\Model(id="NotifyContactModel")
     * @SWG\Property(name="fullname", type="string", required=true, defaultValue=""),
     * @SWG\Property(name="address", type="string", required=false, defaultValue=""),
     * @SWG\Property(name="phone_number", type="string", required=false, defaultValue=""),
     * @SWG\Property(name="email", type="string", required=true, defaultValue=""),
     * @SWG\Property(name="content", type="string", required=false, defaultValue=""),
     * @SWG\Property(name="recaptcha", type="string", required=false, defaultValue=""),
     * @SWG\Api(
     *   path="/backend/notify/contacts",
     *   @SWG\Operation(
     *      method="POST",
     *      summary="Create Notify Contact",
     *      nickname="createNotifyContact",
     *      @SWG\Parameter(name="body", description="Request body", required=true, type="NotifyContactModel", paramType="body", allowMultiple=false),
     *      @SWG\ResponseMessage(code=200, message="OK"),
     *      @SWG\ResponseMessage(code=400, message="Invalid request params"),
     *      @SWG\ResponseMessage(code=401, message="Caller is not authenticated"),
     *      @SWG\ResponseMessage(code=404, message="Resource not found")
     *   )
     * )
     */
    public function store() {
        try {
            $input = $this->request->all();

            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesForCreate());
            if (!empty($validatorErrors)) {
                return $this->respondWithError($validatorErrors);
            }

            $sess = session('recaptcha');
            if (!empty($sess)) {
                if (is_string($sess)) {
                    $recaptcha = $sess;
                } else {
                    $recaptcha = $sess[0];
                }
            } else {
                $recaptcha = null;
            }

            if(!$recaptcha || ($recaptcha !== $input['recaptcha'])) {
                return $this->respondWithErrorKey('recaptcha.required', 400);
            }

            $model = $this->model_repository->create($input);

            // Send Email
            $this->email->send('notify::contact', ['obj' => $model], function ($message) use ($model) {
                $message->to($model->email)->subject('Chúng tôi sẽ phản hồi sớm.');
            });

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
