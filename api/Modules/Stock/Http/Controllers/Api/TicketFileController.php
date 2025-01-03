<?php namespace Modules\Stock\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Stock\Repositories\TicketFileRepository;
use Modules\Stock\Repositories\TicketRepository;

/**
 * Class TicketFileController
 *
 * @package Modules\Stock\Http\Controllers\Api
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2023-05-03
 */
class TicketFileController extends ApiBaseModuleController {
    /**
     * @var string
     */
    protected $module_id = 'stocks';

    /**
     * @var \Modules\Stock\Repositories\TicketFileRepository
     */
    protected $ticket_file_repository;

    public function __construct(Request $request,
                                TicketRepository $ticket_repository,
                                TicketFileRepository $ticket_file_repository) {
        $this->model_repository = $ticket_repository;
        $this->ticket_file_repository = $ticket_file_repository;

        $this->middleware('auth.usr');

        parent::__construct($request);
    }

    /**
     * @OA\Get(
     *   path="/backend/sto_tickets/{id}/files",
     *   summary="Get Ticket Files",
     *   operationId="stoTicketFiles",
     *   tags={"BackendStoTickets"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Ticket Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function files($id) {
        try {
            $queries = ['and' => [['ticket_id', '=', $id]]];
            $results = $this->setUpQueryBuilder($this->ticket_file_repository->getModel(), $queries)->orderBy('id', 'desc')->get();

            return $this->respondWithSuccess($results);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/backend/sto_tickets/{id}/files",
     *   summary="Upload Ticket Files",
     *   operationId="stoUploadTicketFiles",
     *   tags={"BackendStoTickets"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Ticket Id", example=1),
     *   @OA\RequestBody(
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         type="object",
     *         @OA\Property(property="type", type="string", example="att"),
     *         @OA\Property(property="files", type="string", format="binary"),
     *       ),
     *     )
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function fileUploads($id) {
        try {
            // Check permission
            if (!$this->isUpdate($this->module_id)) return $this->errorForbidden();
            $model = $this->model_repository->find($id);
            if (!$model) return $this->errorNotFound();
            //list($files, $errorKeys) = $this->getRequestFiles('files', '*');
            $files = [];
            $errorKeys = [];
            $temps = $this->request->file('files');
            if (!empty($temps)) foreach ($temps as $file) {
                //=== Check file size
                if ($file->getSize() > config('customer.max-total-size')) {
                    $errorKeys[] = 'file.customer.max';
                    continue;
                }
                $files[] = $file;
            }
            if ($errorKeys) return $this->respondWithErrorKey($errorKeys[0]);
            if (!$files) return $this->respondWithErrorKey('file.required');
            $type = $this->request->get('type');
            if (!$type || ($type && !in_array($type, ['att', 'cert']))) $type = 'att';
            // Upload files
            $newFiles = [];
            if (!empty($files)) foreach ($files as $file) {
                $file = $this->ticket_file_repository->createFromFile($file, ['ticket_id' => $model->id, 'owner_id' => $this->auth->id, 'type' => $type]);
                if (!is_string($file)) $newFiles[] = $file;
            }
            $model->files;

            return $this->respondWithSuccess($model);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/backend/sto_tickets/{id}/files/{file_id}",
     *   summary="Delete Ticket File",
     *   operationId="deletePurTicketFile",
     *   tags={"BackendStoTickets"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Ticket Id", example=1),
     *   @OA\Parameter(name="file_id", in="path", description="Ticket File Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function fileDestroy($id, $file_id) {
        try {
            // Check permission
            if (!$this->isDelete($this->module_id)) return $this->errorForbidden();
            $file = $this->ticket_file_repository->findByAttributes(['ticket_id' => $id, 'id' => $file_id]);
            if (!$file) return $this->errorNotFound();
            // Delete model
            $this->ticket_file_repository->destroy($file);

            return $this->respondWithSuccess(trans("Delete success"));
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
