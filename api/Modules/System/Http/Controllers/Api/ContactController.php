<?php namespace Modules\System\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Media\Helpers\FileHelper;
use Modules\System\Repositories\ContactRepository;
use Modules\System\Repositories\SettingRepository;

/**
 * Class ContactController
 *
 * @package Modules\System\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 9/10/2018 4:37 PM
 * @SWG\Resource(
 *   apiVersion="1.0.0",
 *   swaggerVersion="1.2",
 *   resourcePath="/SystemContacts",
 *   description="System Contacts Api"
 * )
 */
class ContactController extends ApiBaseModuleController {
    /**
     * @var \Modules\System\Repositories\SettingRepository
     */
    protected $setting_repository;
    public function __construct(Request $request,
                                ContactRepository $contact_repository,
                                SettingRepository $setting_repository) {
        $this->model_repository = $contact_repository;
        $this->setting_repository = $setting_repository;

        parent::__construct($request);
    }

    /**
     * Get the validation rules for create.
     *
     * @return array
     */
    protected function rulesForCreate() {
        return [
//            'email' => 'required|email',
        ];
    }

    /**
     * Get the validation rules for create.
     *
     * @return array
     */
    protected function rulesRecruitsForCreate() {
        return [
            'email' => 'required|email',
        ];
    }

    /**
     * @SWG\Model(id="SystemContactModel")
     * @SWG\Property(name="name", type="string", required=true, defaultValue=""),
     * @SWG\Property(name="email", type="string", required=false, defaultValue=""),
     * @SWG\Property(name="company", type="string", required=false, defaultValue=""),
     * @SWG\Property(name="phone", type="string", required=true, defaultValue=""),
     * @SWG\Property(name="message", type="string", required=false, defaultValue=""),
     * @SWG\Api(
     *   path="/backend/system_contacts",
     *   @SWG\Operation(
     *      method="POST",
     *      summary="Create System Contact",
     *      nickname="createSystemContact",
     *      @SWG\Parameter(name="body", description="Request body", required=true, type="SystemContactModel", paramType="body", allowMultiple=false),
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

            if ($input['table_content'] and is_string($input['table_content'])) {
                $message = isset($input['message'])?$input['message']:'';
                foreach (json_decode($input['table_content'], true) as $key => $value) {
                    $message .= '<br> ' . $key . $value;
                }
                $input['message'] = $message;
            }

            $file = $this->request->file('file');
            if (!$file) {
//                return $this->respondWithErrorKey('file.required');
            } else {
                $original_name = FileHelper::slug($file->getClientOriginalName());
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $file_mime = finfo_file($finfo, $file);
                finfo_close($finfo);
                $path = '/files/' . date('Ym') . '/' . time() . '-' . $original_name;
                $ok = \Storage::disk(config('filesystems.default'))
                    ->getDriver()->put($path, fopen($file, 'r+'), [
                            'visibility'  => 'public',
                            'ContentType' => $file_mime,
                        ]
                    );
                if (!$ok) {
                    return $this->respondWithErrorKey('file.upload_error');
                }
                $savedFile = [
                    'path'      => $path,
                    'extension' => $file->guessClientExtension(),
                    'mimetype'  => $file->getClientMimeType(),
                    'filesize'  => $file->getFileInfo()->getSize(),
                    'file_url'  => config('filesystems.disks.local.url') . $path
                ];

                $input['file'] = $savedFile['path'];
            }

            $model = $this->model_repository->create($input);

            $emails = [];
            /*$email = $this->setting_repository->findByKey('config_email');
            if (trim($email)) $emails[] = trim($email);*/
            $alert_email = $this->setting_repository->findByKey('config_mail_alert_email');
            if ($alert_email) {
                $alert_emails = explode("\n", str_replace(["\r\n", "\r"], "\n", trim($alert_email)));
                foreach ($alert_emails as $alert_email) {
                    $e2 = explode(',', (string)$alert_email);
                    foreach ($e2 as $email) {
                        if (trim($email)) $emails[] = trim($email);
                    }
                }
            }
            $emails = array_unique($emails);

            // Send Email
            if (!empty($emails)) {
                dispatch(new \Modules\System\Jobs\ContactJob($this->email, $model, $emails));
            }

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @SWG\Model(id="SystemResgiterDownloadModel")
     * @SWG\Property(name="name", type="string", required=true, defaultValue=""),
     * @SWG\Property(name="email", type="string", required=false, defaultValue=""),
     * @SWG\Property(name="company", type="string", required=false, defaultValue=""),
     * @SWG\Property(name="phone", type="string", required=true, defaultValue=""),
     * @SWG\Property(name="message", type="string", required=false, defaultValue=""),
     * @SWG\Property(name="type", type="string", required=false, defaultValue="download"),
     * @SWG\Api(
     *   path="/backend/system_register_download",
     *   @SWG\Operation(
     *      method="POST",
     *      summary="Create System Resgiter Download",
     *      nickname="createSystemResgiterDownload",
     *      @SWG\Parameter(name="body", description="Request body", required=true, type="SystemResgiterDownloadModel", paramType="body", allowMultiple=false),
     *      @SWG\ResponseMessage(code=200, message="OK"),
     *      @SWG\ResponseMessage(code=400, message="Invalid request params"),
     *      @SWG\ResponseMessage(code=401, message="Caller is not authenticated"),
     *      @SWG\ResponseMessage(code=404, message="Resource not found")
     *   )
     * )
     */
    public function registerDownload() {
        try {
            $input = $this->request->all();

            $model = $this->model_repository->create($input);

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @SWG\Model(id="SystemRecruitModel")
     * @SWG\Property(name="name", type="string", required=true, defaultValue=""),
     * @SWG\Property(name="email", type="string", required=false, defaultValue=""),
     * @SWG\Property(name="company", type="string", required=false, defaultValue=""),
     * @SWG\Property(name="phone", type="string", required=true, defaultValue=""),
     * @SWG\Property(name="message", type="string", required=false, defaultValue=""),
     * @SWG\Api(
     *   path="/backend/system_recruits",
     *   @SWG\Operation(
     *      method="POST",
     *      summary="Create System Recruit",
     *      nickname="createSystemRecruit",
     *      @SWG\Parameter(name="body", description="Request body", required=true, type="SystemRecruitModel", paramType="body", allowMultiple=false),
     *      @SWG\ResponseMessage(code=200, message="OK"),
     *      @SWG\ResponseMessage(code=400, message="Invalid request params"),
     *      @SWG\ResponseMessage(code=401, message="Caller is not authenticated"),
     *      @SWG\ResponseMessage(code=404, message="Resource not found")
     *   )
     * )
     */
    public function recruit() {
        try {
            $input = $this->request->all();

            // Check Valid
            $validatorErrors = $this->getValidator($input, $this->rulesRecruitsForCreate());
            if (!empty($validatorErrors)) {
                return $this->respondWithError($validatorErrors);
            }

            $file = $this->request->file('file');
            if (!$file) {
                return $this->respondWithErrorKey('file.required');
            }
            $original_name = FileHelper::slug($file->getClientOriginalName());
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $file_mime = finfo_file($finfo, $file);
            finfo_close($finfo);
            $path = '/files/' . date('Ym') . '/' . time() . '-' . $original_name;
            $ok = \Storage::disk(config('filesystems.default'))
                ->getDriver()->put($path, fopen($file, 'r+'), [
                        'visibility'  => 'public',
                        'ContentType' => $file_mime,
                    ]
                );
            if (!$ok) {
                return $this->respondWithErrorKey('file.upload_error');
            }
            $savedFile = [
                'path'      => $path,
                'extension' => $file->guessClientExtension(),
                'mimetype'  => $file->getClientMimeType(),
                'filesize'  => $file->getFileInfo()->getSize(),
                'file_url'  => config('filesystems.disks.local.url') . $path
            ];

            $input['file'] = $savedFile['path'];
            $input['type'] = 'recruit';

            // Create recruit
            $model = $this->model_repository->create($input);

            $emails = [];
            $email = $this->setting_repository->findByKey('config_email_recruit');
            if (trim($email)) $emails[] = trim($email);
            $alert_email = $this->setting_repository->findByKey('config_mail_alert_email');
            if ($alert_email) {
                $alert_emails = explode("\n", str_replace(["\r\n", "\r"], "\n", trim($alert_email)));
                foreach ($alert_emails as $alert_email) {
                    $e2 = explode(',', (string)$alert_email);
                    foreach ($e2 as $email) {
                        if (trim($email)) $emails[] = trim($email);
                    }
                }
            }

            // Send Email
            if (!empty($emails)) {
                $emails = array_unique($emails);
                $this->email->send('system::recruit', ['obj' => $model], function ($message) use ($model, $emails) {
                    //$message->from(env('MAIL_FROM_ADDRESS', 'motila@motila.vn'), 'ĐƠN ỨNG TUYỂN');
                    foreach ($emails as $email) {
                        $message->to($email)->subject('Đơn ứng tuyển');
                    }
                });
            }

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
