<?php namespace Modules\Business\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Business\Repositories\CategoryRepository;
use Modules\Business\Repositories\ImportHistoryRepository;
use Modules\Business\Repositories\ImportRepository;
use Modules\Business\Repositories\ProductRepository;
use Modules\Business\Repositories\SupplierRepository;
use Modules\Media\Services\FileService;

/**
 * Class OutServiceController
 *
 * @package Modules\Business\Http\Controllers\Api
 * @author Huy D <huydang1920@gmail.com>
 * Date: 2023-04-06
 */
class OutServiceController extends OutProductController {
    /**
     * @var string
     */
    protected $prd_type = PROD_TYPE_OUT_SERVICE;

    public function __construct(Request $request,
                                ImportRepository $import_repository, ImportHistoryRepository $import_history_repository,
                                ProductRepository $product_repository, CategoryRepository $category_repository,
                                SupplierRepository $supplier_repository, FileService $fileService) {
        parent::__construct($request, $import_repository, $import_history_repository, $product_repository, $category_repository, $supplier_repository, $fileService);
    }

    /**
     * @OA\Get(
     *   path="/backend/bus_out_services",
     *   summary="Get Outsourcing Services",
     *   operationId="busGetOutsourcingServices",
     *   tags={"BackendBusOutServices"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="paging", in="query", description="With Paging", example=0),
     *   @OA\Parameter(name="page", in="query", description="Current Page", example=1),
     *   @OA\Parameter(name="pageSize", in="query", description="Item total on page", example=20),
     *   @OA\Parameter(name="sort", in="query", description="Sort by", example="id"),
     *   @OA\Parameter(name="order", in="query", description="Order", example="desc"),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields, extend_fields: Extend fields query} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */

    /**
     * @OA\Get(
     *   path="/backend/bus_out_services/{id}",
     *   summary="Get a Outsourcing Service",
     *   operationId="busGetOutsourcingService",
     *   tags={"BackendBusOutServices"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Outsourcing Service Id", example=1),
     *   @OA\Parameter(name="data", in="query", description="{embed:Optional get related fields, fields: Optional get optional fields} | Syntax: embed=PROPERTYNAME or embed=PROPERTYNAME.CHILDPROPERTYNAME | fields=PROPERTYNAME1,PROPERTYNAME2", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */

    /**
     * @OA\Post(
     *   path="/backend/bus_out_services_products",
     *   summary="Create Outsourcing Service Product",
     *   operationId="busCreateOutsourcingServiceProduct",
     *   tags={"BackendBusProducts"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="idx", type="string", example=""),
     *       @OA\Property(property="category_id", type="integer", example=1),
     *       @OA\Property(property="name", type="string", example=""),
     *       @OA\Property(property="price", type="integer", example=0),
     *       @OA\Property(property="short_description", type="string", example=""),
     *       @OA\Property(property="description", type="string", example=""),
     *       @OA\Property(property="unit", type="string", example="year"),
     *       @OA\Property(property="status", type="integer", example="1"),
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */

    /**
     * @OA\Post(
     *   path="/backend/bus_out_services",
     *   summary="Create Outsourcing Service",
     *   operationId="busCreateOutsourcingService",
     *   tags={"BackendBusOutServices"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="product_id", type="integer", example=1),
     *       @OA\Property(property="idx_im", type="string", example=""),
     *       @OA\Property(property="supplier_id", type="integer", example=1),
     *       @OA\Property(property="price_im", type="integer", example=0),
     *       @OA\Property(property="operating_costs", type="integer", example=0),
     *       @OA\Property(property="expected_profit", type="integer", example=0),
     *       @OA\Property(property="quantity", type="integer", example=1),
     *       @OA\Property(property="earning_ratio", type="integer", example=1),
     *       @OA\Property(property="pretax", type="integer", example=0),
     *       @OA\Property(property="vat", type="integer", example=0),
     *       @OA\Property(property="price", type="integer", example=0),
     *       @OA\Property(property="note", type="string", example=""),
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */

    /**
     * @OA\Put(
     *   path="/backend/bus_out_services/{id}",
     *   summary="Update Outsourcing Service",
     *   operationId="busUpdateOutsourcingService",
     *   tags={"BackendBusOutServices"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Outsourcing Service Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="idx_im", type="string", example=""),
     *       @OA\Property(property="supplier_id", type="integer", example=1),
     *       @OA\Property(property="price_im", type="integer", example=0),
     *       @OA\Property(property="operating_costs", type="integer", example=0),
     *       @OA\Property(property="expected_profit", type="integer", example=0),
     *       @OA\Property(property="quantity", type="integer", example=1),
     *       @OA\Property(property="earning_ratio", type="integer", example=1),
     *       @OA\Property(property="pretax", type="integer", example=0),
     *       @OA\Property(property="vat", type="integer", example=0),
     *       @OA\Property(property="price", type="integer", example=0),
     *       @OA\Property(property="note", type="string", example=""),
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */

    /**
     * @OA\Patch(
     *   path="/backend/bus_out_services/{id}",
     *   summary="Update Outsourcing Service Partial",
     *   operationId="busUpdateOutsourcingServicePartial",
     *   tags={"BackendBusOutServices"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Outsourcing Service Id", example=1),
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="integer", example=0),
     *       @OA\Property(property="note", type="string", example=""),
     *     ),
     *   ),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */

    /**
     * @OA\Delete(
     *   path="/backend/bus_out_services/{id}",
     *   summary="Delete Outsourcing Service",
     *   operationId="busDeleteOutsourcingService",
     *   tags={"BackendBusOutServices"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Outsourcing Service Id", example=1),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params"),
     *   @OA\Response(response=401, description="Caller is not authenticated"),
     *   @OA\Response(response=404, description="Resource not found"),
     * )
     */
}
