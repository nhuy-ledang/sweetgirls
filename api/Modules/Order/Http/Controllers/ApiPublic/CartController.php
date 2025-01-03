<?php namespace Modules\Order\Http\Controllers\ApiPublic;

use Illuminate\Http\Request;
use Modules\Order\Repositories\CartRepository;
use Modules\Order\Repositories\OrderRepository;
use Modules\Order\Transport\Facade\Transport;
use Modules\Product\Repositories\ProductRepository;
use Modules\System\Repositories\SettingRepository;
use Modules\User\Repositories\UserRankRepository;
use Modules\User\Repositories\UserRepository;

/**
 * Class CartController
 *
 * @package Modules\Order\Http\Controllers\ApiPublic
 */
class CartController extends ApiBaseModuleController {
    /**
     * @var \Modules\Product\Repositories\ProductRepository
     */
    protected $product_repository;

    /**
     * @var \Modules\System\Repositories\SettingRepository
     */
    protected $setting_repository;
    /**
     * @var \Modules\User\Repositories\Sentinel\SentinelUserRepository
     */
    protected $user_repository;

    /**
     * @var \Modules\User\Repositories\UserRankRepository
     */
    protected $user_rank_repository;

    /**
     * @var \Modules\Order\Repositories\OrderRepository
     */
    protected $order_repository;

    public function __construct(Request $request,
                                CartRepository $cart_repository,
                                ProductRepository $product_repository,
                                SettingRepository $setting_repository,
                                UserRepository $user_repository,
                                UserRankRepository $user_rank_repository,
                                OrderRepository $order_repository) {
        $this->model_repository = $cart_repository;
        $this->product_repository = $product_repository;
        $this->coupon_repository = $coupon_repository;
        $this->voucher_repository = $voucher_repository;
        $this->setting_repository = $setting_repository;
        $this->user_repository = $user_repository;
        $this->user_rank_repository = $user_rank_repository;
        $this->order_repository = $order_repository;

        $this->middleware('auth.user')->only(['addVoucher']);

        parent::__construct($request);
    }

    private function getCartProducts($user_id, $session_id, $tz = -420, $refresh = false) {
        // Get products
        $results = $this->model_repository->getProducts($user_id, $session_id, $tz, $refresh);
        $products = [];
        foreach ($results as $item) {
            $newItem = $this->parseToRespond($item);
            $product = new \Modules\Product\Entities\Product($newItem['pd']);
            $product->id = $newItem['product_id'];
            $product->special = $newItem['specialo'];
            $reduce = false;
            if ($product->price && $product->special) {
                $reduce = '-' . round((($product->price - $product->special) / $product->price) * 100) . '%';
            }
            $product->reduce = $reduce;
            unset($newItem['pd']);
            unset($newItem['specialo']);
            $newItem['product'] = $product->toArray();
            $products[] = $newItem;
        }

        return $products;
    }

    private function getCartTotals($user_id, $session_id, $tz = -420) {
        // Totals
        list($sub_total, $discount_code, $discount_total, $voucher_code, $voucher_total, $shipping_code, $shipping_fee, $shipping_discount, $vat, $total, $included_total, $coupon, $next_gifts, $current_gifts) = $this->model_repository->getTotals($user_id, $session_id, $tz);
        $totals = [];
        $totals['sub_total'] = $sub_total;
        $totals['discount_code'] = $discount_code;
        $totals['discount_total'] = $discount_total;
        $totals['voucher_code'] = $voucher_code;
        $totals['voucher_total'] = $voucher_total;
        $totals['shipping_code'] = $shipping_code;
        $totals['shipping_fee'] = $shipping_fee;
        $totals['shipping_discount'] = $shipping_discount;
        $totals['included_total'] = $included_total;
        $totals['total'] = $total;
        $totals['coins'] = $this->model_repository->getCoins($user_id, $session_id, $tz);

        // Gift order
        $gift_orders = [];
        $gift_orders['next_gifts'] = $next_gifts;
        $gift_orders['current_gifts'] = $current_gifts;

        return [$totals, $coupon, $gift_orders];
    }

    private function getCart($user_id, $session_id, $tz = -420, $refresh = false) {
        $data = [];
        // Get products
        $data['products'] = $this->getCartProducts($user_id, $session_id, $tz, $refresh);
        // Totals
        list($totals, $coupon, $gift_orders) = $this->getCartTotals($user_id, $session_id, $tz);
        $data['totals'] = $totals;
        // Coupon info
        $data['coupon'] = $coupon;
        // Gift orders
        $data['gift_orders'] = $gift_orders;
        $data['no_cod'] = in_array(true, array_column($data['products'], 'no_cod'));

        return $data;
    }

    /**
     * @OA\Get(
     *   path="/carts",
     *   summary="Get Carts",
     *   operationId="getCarts",
     *   tags={"Carts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="tz", in="query", description="TimezoneOffset", example="-420"),
     *   @OA\Parameter(name="data", in="query", description="{extend_fields: included_products}", example=""),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function index() {
        try {
            $tz = (int)$this->request->get('tz');
            $user_id = $this->isLogged() ? $this->auth->id : null;
            $session_id = $this->getSessionId();
            $data = $this->getCart($user_id, $session_id, $tz);
            // Get more
            $extend_fields = $this->getRequestFields('extend_fields');
            if (in_array('included_products', $extend_fields)) {
                $data['included_products'] = $this->product_repository->getModel()->where('is_included', 1)->where('status', 1)->orderBy('name', 'asc')->limit(10)->select(['id', 'name', 'price', 'short_description'])->get();
            }

            return $this->respondWithSuccess($data);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/carts",
     *   summary="Add Cart",
     *   operationId="addCart",
     *   tags={"Carts"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"product_id", "quantity"},
     *       @OA\Property(property="product_id", type="integer", example=1),
     *       @OA\Property(property="quantity", type="integer", example=1),
     *     ),
     *   ),
     *   @OA\Parameter(name="tz", in="query", description="TimezoneOffset", example="-420"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function store() {
        try {
            $tz = (int)$this->request->get('tz');
            $user_id = $this->isLogged() ? $this->auth->id : null;
            $session_id = $this->getSessionId();
            $input = $this->request->only(['product_id', 'quantity']);
            $validatorErrors = $this->getValidator($input, ['product_id' => 'required|integer', 'quantity' => 'required|integer|min:1']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $this->model_repository->addCart($user_id, $session_id, $input['product_id'], $input['quantity']);
            $data = $this->getCart($user_id, $session_id, $tz);

            return $this->respondWithSuccess($data);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *   path="/carts/{id}",
     *   summary="Update Cart",
     *   operationId="updateCart",
     *   tags={"Carts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Cart Id", example=1),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"quantity"},
     *       @OA\Property(property="quantity", type="integer", example=1),
     *     ),
     *   ),
     *   @OA\Parameter(name="tz", in="query", description="TimezoneOffset", example="-420"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function update($id) {
        try {
            $tz = (int)$this->request->get('tz');
            $user_id = $this->isLogged() ? $this->auth->id : null;
            $session_id = $this->getSessionId();
            $quantity = (int)$this->request->get('quantity');
            if (!$quantity || $quantity < 0) $quantity = 0;
            if (!$quantity) return $this->destroy($id);
            $model = $this->model_repository->getCart($user_id, $session_id, $id);
            if ($model) {
                if ($model->type == 'G') {
                    if (!$user_id) return $this->respondWithErrorKey('system.unauthorized', 401);
                    // Check product valid
                    $product = $this->product_repository->getModel()->where('id', $model->product_id)->where('is_gift', 1)->where('coins', '>', 0)->where('status', 1)->select(['id', 'coins'])->first();
                    if (!$product) return $this->errorWrongArgs('product_id.required');
                    // Check have enough coin
                    if ($this->auth->coins == 0) return $this->errorWrongArgs('cart.coins.invalid');
                    $coins = $this->model_repository->checkCoins($user_id, $session_id, $product->id, $quantity);
                    if ($this->auth->coins < $coins) return $this->errorWrongArgs('cart.coins.invalid');
                } else if ($model->type == 'I') {
                    return $this->errorWrongArgs('cart.include_product.exists');
                }
                $this->model_repository->updateCart($user_id, $session_id, $id, $quantity);
            }
            $data = $this->getCart($user_id, $session_id, $tz);

            return $this->respondWithSuccess($data);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/carts/{id}",
     *   summary="Delete a Cart",
     *   operationId="deleteCart",
     *   tags={"Carts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="id", in="path", description="Cart Id", example=1),
     *   @OA\Parameter(name="tz", in="query", description="TimezoneOffset", example="-420"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function destroy($id) {
        try {
            $tz = (int)$this->request->get('tz');
            $user_id = $this->isLogged() ? $this->auth->id : null;
            $session_id = $this->getSessionId();
            $this->model_repository->removeCart($user_id, $session_id, $id);
            $data = $this->getCart($user_id, $session_id, $tz);

            return $this->respondWithSuccess($data);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/carts_coins",
     *   summary="Add Product By Coin To Cart",
     *   operationId="addProductByCoinToCart",
     *   tags={"Carts"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="product_id", type="integer", example=1),
     *     ),
     *   ),
     *   @OA\Parameter(name="tz", in="query", description="TimezoneOffset", example="-420"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function addByCoin() {
        try {
            $tz = (int)$this->request->get('tz');
            $user_id = $this->isLogged() ? $this->auth->id : null;
            if (!$user_id) return $this->respondWithErrorKey('system.unauthorized', 401);
            $session_id = $this->getSessionId();
            $product_id = $this->request->get('product_id');
            $validatorErrors = $this->getValidator(compact('product_id'), ['product_id' => 'required|integer']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Check product valid
            $product = $this->product_repository->getModel()->where('id', $product_id)->where('is_gift', 1)->where('coins', '>', 0)->where('status', 1)->select(['id', 'coins'])->first();
            if (!$product) return $this->errorWrongArgs('product_id.required');
            // Check have enough coin
            if ($this->auth->coins == 0) return $this->errorWrongArgs('cart.coins.invalid');
            $coins = $this->model_repository->checkCoins($user_id, $session_id, $product_id);
            if ($this->auth->coins < $coins) return $this->errorWrongArgs('cart.coins.invalid');
            // Check coin expired
            $dateNow = $this->getDateLocalFromTz($tz,'Y-m-d');
            if ($this->auth->coins_expired && $this->auth->coins_expired < $dateNow) return $this->errorWrongArgs('cart.coins.expired');
            // Check cart valid
            $valid = $this->model_repository->isValidCart($user_id, $session_id);
            if (!$valid) return $this->errorWrongArgs('cart.empty');
            // Add cart
            $this->model_repository->addCart($user_id, $session_id, $product_id, 1, 'G');
            $data = $this->getCart($user_id, $session_id, $tz, true);

            return $this->respondWithSuccess($data);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/carts_includes",
     *   summary="Add Include Product To Cart",
     *   operationId="addIncludeProductToCart",
     *   tags={"Carts"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="product_id", type="integer", example=1),
     *     ),
     *   ),
     *   @OA\Parameter(name="tz", in="query", description="TimezoneOffset", example="-420"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function addIncludeProduct() {
        try {
            $tz = (int)$this->request->get('tz');
            $user_id = $this->isLogged() ? $this->auth->id : null;
            $session_id = $this->getSessionId();
            $product_id = $this->request->get('product_id');
            $validatorErrors = $this->getValidator(compact('product_id'), ['product_id' => 'required|integer']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Check product valid
            $product = $this->product_repository->getModel()->where('id', $product_id)->where('is_included', 1)->where('status', 1)->select(['id'])->first();
            if (!$product) return $this->errorWrongArgs('product_id.required');
            $cart = $this->model_repository->getCartByProduct($user_id, $session_id, $product_id);
            if ($cart) return $this->errorWrongArgs('cart.include_product.exists');
            // Check cart valid
            $valid = $this->model_repository->isValidCart($user_id, $session_id);
            if (!$valid) return $this->errorWrongArgs('cart.empty');
            // Add cart
            $this->model_repository->addCart($user_id, $session_id, $product_id, 1, 'I');
            $data = $this->getCart($user_id, $session_id, $tz, true);

            return $this->respondWithSuccess($data);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/carts_products/{product_id}",
     *   summary="Delete Product Cart",
     *   operationId="crtDeleteProductCart",
     *   tags={"Carts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="product_id", in="path", description="Product Id", example=1),
     *   @OA\Parameter(name="tz", in="query", description="TimezoneOffset", example="-420"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function removeProduct($product_id) {
        try {
            $tz = (int)$this->request->get('tz');
            $user_id = $this->isLogged() ? $this->auth->id : null;
            $session_id = $this->getSessionId();
            $this->model_repository->removeCartProduct($user_id, $session_id, $product_id);
            $data = $this->getCart($user_id, $session_id, $tz);

            return $this->respondWithSuccess($data);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *   path="/carts_totals",
     *   summary="Get Cart Totals",
     *   operationId="getCartTotals",
     *   tags={"Carts"},
     *   security={{"bearer":{}}},
     *   @OA\Parameter(name="tz", in="query", description="TimezoneOffset", example="-420"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function totals() {
        try {
            $user_id = $this->isLogged() ? $this->auth->id : null;
            $session_id = $this->getSessionId();
            // Totals
            list($totals, $coupon) = $this->getCartTotals($user_id, $session_id);

            return $this->respondWithSuccess($totals);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/carts_coupon",
     *   summary="Add Coupon To Cart",
     *   operationId="addCouponToCart",
     *   tags={"Carts"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       required={"coupon"},
     *       @OA\Property(property="coupon", type="string", example=""),
     *     ),
     *   ),
     *   @OA\Parameter(name="tz", in="query", description="TimezoneOffset", example="-420"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function addCoupon() {
        try {
            $input = $this->request->only(['coupon']);
            $validatorErrors = $this->getValidator($input, ['coupon' => 'required']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            // Check valid
            $tz = (int)$this->request->get('tz');
            $dateNow = $this->getDateLocalFromTz($tz, 'Y-m-d H:i:s');
            $session_id = $this->getSessionId();
            $user_id = $this->isLogged() ? $this->auth->id : null;
            $user_rank = 0;
            if ($user_id) {
                $ranks = $this->user_rank_repository->getModel()->where('status', 1)->orderBy('rank', 'asc')->get();
                foreach ($ranks as $rank) if ($this->auth->points >= $rank->value) $user_rank = $rank->rank;
            }
            $coupon = $this->coupon_repository->getModel()->where('code', $input['coupon'])
                ->where('status', 1)
                ->whereRaw("(`limited` is null or (`limited` is not null and `uses_total` < `limited`))");

            if ($user_id) $coupon = $coupon->whereRaw("(`limit_per_customer` is null or (`limit_per_customer` is not null and `limit_per_customer` > (select count(*) from `mkt__coupon_histories` where `coupon_id` = `mkt__coupons`.`id` and `user_id` = $user_id)))");
            $coupon = $coupon->whereRaw("(`rank_id` is null or (`rank_id` is not null and `rank_id` = $user_rank))")
                ->whereRaw("((`start_date` is null or UNIX_TIMESTAMP(`start_date`) <= UNIX_TIMESTAMP('$dateNow')) and (`end_date` is null or UNIX_TIMESTAMP('$dateNow') <= UNIX_TIMESTAMP(`end_date`)))")
                ->select(['id', 'name', 'code', 'end_date', 'product_ids', 'category_ids', 'rule', 'rank_id', 'total', 'max_discount', 'new_member'])->first();
            if (!$coupon) {
                // Get voucher
                $voucher = $this->voucher_repository->getModel()->where('code', $input['coupon'])
                    ->whereRaw("((`start_date` is null or UNIX_TIMESTAMP(`start_date`) <= UNIX_TIMESTAMP('$dateNow')) and (`end_date` is null or UNIX_TIMESTAMP('$dateNow') <= UNIX_TIMESTAMP(`end_date`)))")
                    ->select(['id', 'code', 'end_date'])->first();
                if (!$voucher) return $this->errorWrongArgs('coupon.invalid');
                $this->clearCoupon();
                return $this->addVoucher($input['coupon']);
            } else {
                // Check coupon valid
                $sub_total = $this->model_repository->getSubTotal($user_id, $session_id, $tz);
                $cart_products = $this->getCartProducts($user_id, $session_id);
                if (0 < $coupon->total && $sub_total < $coupon->total) {
                    $message = sprintf('Đơn hàng phải >= %s để áp dụng mã giảm giá', number_format($coupon->total, 0, ',', '.'));
                    return $this->respondWithErrorKey('voucher_code.order_total', 400, $message);
                }
                if ($coupon->product_ids) {
                    // COUPON EXIST PRODUCT IDS
                    $product_arr = explode(",", $coupon->product_ids);
                    $cart_product_ids = array_column($cart_products, 'product_id');
                    if ($coupon->rule === COUPON_RULE_INCLUDE) {
                        $exist_product = empty(array_intersect($product_arr, $cart_product_ids));
                        if ($exist_product) return $this->errorWrongArgs('coupon.product.invalid');
                    } else if ($coupon->rule === COUPON_RULE_EXCLUDE) {
                        $exist_other_product = empty(array_diff($cart_product_ids, $product_arr));
                        if($exist_other_product) return $this->errorWrongArgs('coupon.product.invalid');
                    }
                }
                if ($coupon->category_ids) {
                    // COUPON EXIST CATEGORY IDS
                    $category_arr = explode(",", $coupon->category_ids);
                    $cart_category_ids = array_column($cart_products, 'category_id');
                    if ($coupon->rule === COUPON_RULE_INCLUDE) {
                        $exist_category = empty(array_intersect($category_arr, $cart_category_ids));
                        if ($exist_category) return $this->errorWrongArgs('coupon.category.invalid');
                    } else if ($coupon->rule === COUPON_RULE_EXCLUDE) {
                        $exist_other_category = empty(array_diff($cart_category_ids, $category_arr));
                        if($exist_other_category) return $this->errorWrongArgs('coupon.category.invalid');
                    }
                }
                if ($coupon->new_member) {
                    if (!$user_id) return $this->errorWrongArgs('coupon.user.new_member');
                    $exits_order = $this->order_repository->getModel()->where('user_id', $user_id)->exists();
                    if ($exits_order) return $this->errorWrongArgs('coupon.user.new_member');
                }
            }
            // Add coupon to session
            $this->addVoucher();
            $this->model_repository->addCoupon($user_id, $session_id, $coupon->code);
            $data = [];
            // Totals
            list($totals, $coupon) = $this->getCartTotals($user_id, $session_id);
            $data['totals'] = $totals;
            // Coupon info
            $data['coupon'] = $coupon;

            return $this->respondWithSuccess($data);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *   path="/carts_coupon",
     *   summary="Clear Coupon From Cart",
     *   operationId="clearCouponFromCart",
     *   tags={"Carts"},
     *   security={{"bearer":{}}},
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function clearCoupon() {
        try {
            // Add coupon to session
            $user_id = $this->isLogged() ? $this->auth->id : null;
            $session_id = $this->getSessionId();
            $this->model_repository->addCoupon($user_id, $session_id, null);
            $data = [];
            // Totals
            list($totals, $coupon) = $this->getCartTotals($user_id, $session_id);
            $data['totals'] = $totals;
            // Coupon info
            $data['coupon'] = $coupon;

            return $this->respondWithSuccess($data);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/carts_voucher",
     *   summary="Add Voucher To Cart",
     *   operationId="crtAddVoucher",
     *   tags={"Carts"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="voucher", type="string", example=""),
     *     ),
     *   ),
     *   @OA\Parameter(name="tz", in="query", description="TimezoneOffset", example="-420"),
     *   @OA\Response(response=200, description="Success", @OA\JsonContent()),
     *   @OA\Response(response=400, description="Invalid request params", @OA\JsonContent()),
     *   @OA\Response(response=401, description="Caller is not authenticated", @OA\JsonContent()),
     *   @OA\Response(response=403, description="Wrong credentials response", @OA\JsonContent()),
     *   @OA\Response(response=404, description="Resource not found", @OA\JsonContent()),
     *   @OA\Response(response=500, description="Internal Server Error", @OA\JsonContent())
     * )
     */
    public function addVoucher($code = '') {
        try {
            $tz = (int)$this->request->get('tz');
            $voucher_code = $this->request->get('voucher');
            $dateNow = $this->getDateLocalFromTz($tz, 'Y-m-d H:i:s');
            if (!$voucher_code) $voucher_code = $code;
            if ($voucher_code) $voucher_code = strtoupper(trim($voucher_code));
            $session_id = $this->getSessionId();
            $user_id = $this->auth ? $this->auth->id : null;
            if ($voucher_code) {
                $validatorErrors = $this->getValidator(compact('voucher_code'), ['voucher_code' => 'required']);
                if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
                // Check voucher valid
                //if (!$this->auth) return $this->respondWithErrorKey('voucher_code.invalid');
                $voucher = $this->voucher_repository->getModel()->where('user_id', $user_id)->where('code', $voucher_code)->where('quantity', '>', 0)
                    ->whereRaw("((`start_date` is null or UNIX_TIMESTAMP('$dateNow') >= UNIX_TIMESTAMP(`start_date`)) and (`end_date` is null or UNIX_TIMESTAMP('$dateNow') <= UNIX_TIMESTAMP(`end_date`)))")
                    ->first();
                if (!$voucher) return $this->respondWithErrorKey('voucher_code.invalid');
                $sub_total = $this->model_repository->getSubTotal($user_id, $session_id, $tz);
                // Check voucher valid
                if (0 < $voucher->total && $sub_total < $voucher->total) {
                    $message = sprintf('Đơn hàng phải >= %s để áp dụng mã voucher', number_format($voucher->total, 0, ',', '.'));
                    return $this->respondWithErrorKey('voucher_code.order_total', 400, $message);
                }
                $cart_products = $this->getCartProducts($user_id, $session_id);
                if ($voucher->product_ids) {
                    // COUPON EXIST PRODUCT IDS
                    $product_arr = explode(",", $voucher->product_ids);
                    $cart_product_ids = array_column($cart_products, 'product_id');
                    if ($voucher->rule === COUPON_RULE_INCLUDE) {
                        $exist_product = empty(array_intersect($product_arr, $cart_product_ids));
                        if ($exist_product) return $this->errorWrongArgs('coupon.product.invalid');
                    } else if ($voucher->rule === COUPON_RULE_EXCLUDE) {
                        $exist_other_product = empty(array_diff($cart_product_ids, $product_arr));
                        if($exist_other_product) return $this->errorWrongArgs('coupon.product.invalid');
                    }
                }
                if ($voucher->category_ids) {
                    // COUPON EXIST CATEGORY IDS
                    $category_arr = explode(",", $voucher->category_ids);
                    $cart_category_ids = array_column($cart_products, 'category_id');
                    if ($voucher->rule === COUPON_RULE_INCLUDE) {
                        $exist_category = empty(array_intersect($category_arr, $cart_category_ids));
                        if ($exist_category) return $this->errorWrongArgs('coupon.category.invalid');
                    } else if ($voucher->rule === COUPON_RULE_EXCLUDE) {
                        $exist_other_category = empty(array_diff($cart_category_ids, $category_arr));
                        if($exist_other_category) return $this->errorWrongArgs('coupon.category.invalid');
                    }
                }
                // Add voucher
                $this->model_repository->addVoucher($user_id, $session_id, $voucher->code);
            } else {
                $this->model_repository->addVoucher($user_id, $session_id, null);
            }
            $data = [];
            // Totals
            list($totals, $coupon) = $this->getCartTotals($user_id, $session_id);
            $data['totals'] = $totals;

            return $this->respondWithSuccess($data);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/shipping_services",
     *   summary="Get Shipping Services",
     *   operationId="getShippingServices",
     *   tags={"Carts"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="province_id", type="integer", example=0),
     *       @OA\Property(property="district_id", type="integer", example=0),
     *       @OA\Property(property="ward_id", type="integer", example=0),
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
    public function getShippingServices() {
        try {
            $input = $this->request->only(['province_id', 'district_id', 'ward_id']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, ['province_id' => 'required|integer', 'district_id' => 'required|integer', 'ward_id' => 'required|integer']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $user_id = $this->isLogged() ? $this->auth->id : null;
            $session_id = $this->getSessionId();
            $weight = $this->model_repository->getWeight($user_id, $session_id);
            list($sub_total, $discount_code, $discount_total, $voucher_code, $voucher_total, $shipping_code, $shipping_fee, $shipping_discount, $vat, $total, $included_total, $coupon) = $this->model_repository->getTotals($user_id, $session_id);
            $params = [
                'SENDER_PROVINCE'   => (int)$this->setting_repository->findByKey('config_ord_province', 0),
                'SENDER_DISTRICT'   => (int)$this->setting_repository->findByKey('config_ord_district', 0),
                'SENDER_WARD'   => (int)$this->setting_repository->findByKey('config_ord_ward', 0),
                'RECEIVER_PROVINCE' => $input['province_id'],
                'RECEIVER_DISTRICT' => $input['district_id'],
                'RECEIVER_WARD' => $input['ward_id'],
                'PRODUCT_WEIGHT'    => $weight,
                'PRODUCT_PRICE'     => $total,
                'MONEY_COLLECTION'  => $total,
            ];

            $res = Transport::getPriceAll($params);
            return $this->respondWithSuccess($res);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *   path="/shipping_fee",
     *   summary="Get Shipping Fee",
     *   operationId="getShippingFee",
     *   tags={"Carts"},
     *   security={{"bearer":{}}},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       @OA\Property(property="province_id", type="integer", example=0),
     *       @OA\Property(property="district_id", type="integer", example=0),
     *       @OA\Property(property="shipping_code", type="string", example=""),
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
    public function getShippingFee() {
        try {
            $input = $this->request->only(['province_id', 'district_id', 'ward_id', 'shipping_code']);
            // Check Valid
            $validatorErrors = $this->getValidator($input, ['province_id' => 'required|integer', 'district_id' => 'required|integer', 'ward_id' => 'required|integer', 'shipping_code' => 'required']);
            if (!empty($validatorErrors)) return $this->respondWithError($validatorErrors);
            $user_id = $this->isLogged() ? $this->auth->id : null;
            $session_id = $this->getSessionId();
            $weight = $this->model_repository->getWeight($user_id, $session_id);
            list($sub_total, $discount_code, $discount_total, $voucher_code, $voucher_total, $shipping_code, $shipping_fee, $shipping_discount, $vat, $total, $included_total, $coupon) = $this->model_repository->getTotals($user_id, $session_id);
            list($res, $shipping_discount) = $this->handleShippingFee($input['province_id'], $input['district_id'], $input['ward_id'], $input['shipping_code'], $weight, $total, $user_id);
            $this->model_repository->addShipping($user_id, $session_id, $input['shipping_code'], $res['MONEY_TOTAL'], $shipping_discount);
            $data = [];
            // Totals
            list($totals, $coupon) = $this->getCartTotals($user_id, $session_id);
            $data['totals'] = $totals;
            // Coupon info
            $data['coupon'] = $coupon;

            return $this->respondWithSuccess($data);
        } catch (\Exception $e) {
            return $this->errorInternalError($e->getMessage());
        }
    }

    public function handleShippingFee($province_id, $district_id, $ward_id, $shipping_code, $weight, $total, $user_id) {
        $params = [
            'SENDER_PROVINCE'   => (int)$this->setting_repository->findByKey('config_ord_province', 0),
            'SENDER_DISTRICT'   => (int)$this->setting_repository->findByKey('config_ord_district', 0),
            'SENDER_WARD'       => (int)$this->setting_repository->findByKey('config_ord_ward', 0),
            'RECEIVER_PROVINCE' => $province_id,
            'RECEIVER_DISTRICT' => $district_id,
            'RECEIVER_WARD'     => $ward_id,
            'PRODUCT_WEIGHT'    => $weight,
            'PRODUCT_PRICE'     => $total,
            'MONEY_COLLECTION'  => $total,
            'ORDER_SERVICE_ADD' => '',
            'ORDER_SERVICE'     => $shipping_code,
        ];
        $res = Transport::getPrice($params);
        // Shipping discount
        $shipping_discount = 0;
        if ($user_id) {
            $model = $this->user_repository->getModel()->where('id', $user_id)->first();
            $user_point = $model->points;
            $ranks = $this->user_rank_repository->getModel()->where('status', 1)->orderBy('rank', 'asc')->get();
            $user_rank = 0;
            foreach ($ranks as $rank) if ($user_point >= $rank->value) $user_rank = $rank->rank;
            // 2 : SILVER MEMBER, 3 : GOLD MEMBER, 4 : DIAMOND MEMBER
            if ($user_rank === 2) $shipping_discount = min($res['MONEY_TOTAL'], 10000);
            else if ($user_rank === 3) $shipping_discount = min($res['MONEY_TOTAL'], 20000);
            else if ($user_rank === 4) $shipping_discount = $shipping_code !== 'VHT' ? $res['MONEY_TOTAL'] : 0;
        }

        return [$res, $shipping_discount];
    }
}
