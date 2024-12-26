<?php namespace Modules\Notify\Http\Controllers\Api;

use Illuminate\Http\Request;

/**
 * Class RecaptchaController
 *
 * @package Modules\Notify\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 9/10/2018 4:37 PM
 * @SWG\Resource(
 *   apiVersion="1.0.0",
 *   swaggerVersion="1.2",
 *   resourcePath="/NotifyRecaptcha",
 *   description="Notify Recaptcha Api"
 * )
 */
class RecaptchaController extends ApiBaseModuleController {
    public function __construct(Request $request) {
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
            'recaptcha' => 'required'
        ];
    }

    /**
     * @SWG\Model(id="NotifyRecaptchaModel")
     * @SWG\Property(name="recaptcha", type="string", required=true, defaultValue=""),
     * @SWG\Api(
     *   path="/backend/notify/recaptcha",
     *   @SWG\Operation(
     *      method="POST",
     *      summary="Create Notify Recaptcha",
     *      nickname="createNotifyRecaptcha",
     *      @SWG\Parameter(name="body", description="Request body", required=true, type="NotifyRecaptchaModel", paramType="body", allowMultiple=false),
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

            session()->forget('recaptcha');
            session()->push('recaptcha', $input['recaptcha']);

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

            return $this->respondWithSuccess($recaptcha);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
