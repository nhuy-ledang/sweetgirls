<?php namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UserController extends Controller {

    /**
     * Illuminate\Http\Request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * @var \Modules\User\Repositories\Authentication
     */
    protected $_auth;

    public function __construct(Request $request) {
        $this->request = $request;
        $this->_auth = app('Modules\User\Repositories\Authentication');
    }

    // Like Api: /auth/register-email-verify
    public function emailVerify() {
        $code = $this->request->get('c');
        $email = $this->request->get('e');

        // Check Valid
        if (!$code || !$email) {
            return view('user::emailVerify', ['user' => false]);
        }

        $model = $this->_auth->findByEmail($email);
        if (!$model) {
            return view('user::emailVerify', ['user' => false]);
        }

        if ($model->email_verified) {
            return view('user::emailVerify', ['user' => false]);
        }

        $user = $this->_auth->completeChangeBySMS($model, 'email', $code);

        return view('user::emailVerify', ['user' => $user]);
    }
}
