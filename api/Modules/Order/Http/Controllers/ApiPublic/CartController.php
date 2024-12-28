<?php namespace Modules\Order\Http\Controllers\ApiPublic;

use Illuminate\Http\Request;
use Modules\Order\Repositories\CartRepository;
use Modules\Order\Repositories\OrderRepository;
use Modules\Product\Repositories\ProductRepository;
use Modules\System\Repositories\SettingRepository;
use Modules\User\Repositories\UserRepository;

/**
 * Class CartController
 *
 * @package Modules\Order\Http\Controllers\ApiPublic
 * @author Huy Dang <huydang1920@gmail.com>
 * Date: 2022-06-03
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
     * @var \Modules\Order\Repositories\OrderRepository
     */
    protected $order_repository;

    public function __construct(Request $request,
                                CartRepository $cart_repository,
                                ProductRepository $product_repository,
                                SettingRepository $setting_repository,
                                UserRepository $user_repository,
                                OrderRepository $order_repository) {
        $this->model_repository = $cart_repository;
        $this->product_repository = $product_repository;
        $this->setting_repository = $setting_repository;
        $this->user_repository = $user_repository;
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
}
