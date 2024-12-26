<?php namespace Modules\Core\Http\Controllers\ApiPublic;

use Illuminate\Http\Request;
use Modules\Core\Repositories\BankRepository;

/**
 * Class BankController
 * @package Modules\Core\Http\Controllers\Api
 * @author Huy D <huydang1920@gmail.com>
 * @copyright (c) Motila Corporation
 */
class BankController extends ApiBaseModuleController {
    public function __construct(Request $request, BankRepository $bank_repository) {
        $this->model_repository = $bank_repository;

        parent::__construct($request);
    }

    /**
     * @OA\Get(
     *   path="/core_banks",
     *   summary="Get Bank All",
     *   operationId="getBankAll",
     *   tags={"CoreBanks"},
     *   security={{"bearer":{}}},
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function all() {
        try {
            $results = $this->model_repository->getModel()->orderBy('name', 'asc')->get();

            return $this->respondWithSuccess($results);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
