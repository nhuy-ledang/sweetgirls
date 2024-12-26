<?php namespace Modules\User\Http\Controllers\ApiPublic;

use Illuminate\Http\Request;

/**
 * Class VoucherController
 *
 * @package Modules\User\Http\Controllers\ApiPublic
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2023-07-11
 */
class VoucherController extends ApiBaseModuleController {
    public function __construct(Request $request) {
        $this->model_repository = $voucher_repository;

        $this->middleware('auth.user');

        parent::__construct($request);
    }

    /**
     * @OA\Get(
     *   path="/user_vouchers",
     *   summary="Get Vouchers",
     *   operationId="userGetVouchers",
     *   tags={"UserVouchers"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="paging", in="query", description="With Paging", example="0"),
     *   @OA\Parameter(name="page", in="query", description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query", description="Item total on page", example=20),
     *   @OA\Parameter(name="sort", in="query", description="Sort by", example="name"),
     *   @OA\Parameter(name="order", in="query", description="Order", example="asc"),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields, extend_fields: avgReview} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
    public function index() {
        try {
            $page = (int)$this->request->get('page');
            $pageSize = (int)$this->request->get('pageSize');
            if (!$page) $page = 1;
            if (!$pageSize) $pageSize = $this->pageSize;
            if ($this->maximumLimit && $pageSize > $this->maximumLimit) $pageSize = $this->maximumLimit;
            $sort = (string)$this->request->get('sort');
            $order = (string)$this->request->get('order');
            $sort = !$sort ? 'id' : strtolower($sort);
            $order = !$order ? 'desc' : strtolower($order);
            $queries = ['and' => [['user_id', '=', $this->auth->id]]];
            //$data = $this->getRequestData();
            /*$q = trim(utf8_strtolower((isset($data->{'q'}) && !is_null($data->{'q'}) && $data->{'q'} !== '') ? trim((string)$data->{'q'}) : ''));
            if ($q) $queries['whereRaw'][] = ["lower(`name`) like ?", "%$q%"];*/
            $results = $this->setUpQueryBuilder($this->model(), $queries, false)->orderBy($sort, $order)->take($pageSize)->skip($pageSize * ($page - 1))->get();
            $paging = $this->request->get('paging');
            $paging = is_null($paging) || $paging == 'false' ? false : ($paging == 'true' ? true : (boolean)$paging);
            if (!$paging) {
                return $this->respondWithSuccess($results);
            } else {
                $totalCount = $this->setUpQueryBuilder($this->model(), $queries, true)->count();
                return $this->respondWithPaging($results, $totalCount, $pageSize, $page);
            }
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }
}
