<?php namespace Modules\Order\Repositories\Eloquent;

use Modules\Core\Traits\CommonTrait;
use Modules\Marketing\Entities\Coupon;
use Modules\Marketing\Entities\CouponHistory;
use Modules\Marketing\Entities\CouponProduct;
use Modules\Marketing\Entities\Voucher;
use Modules\Product\Entities\GiftOrder;
use Modules\Product\Entities\GiftOrderProduct;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductDiscount;
use Modules\Product\Entities\ProductSpecial;
use Modules\Order\Repositories\CartRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;

class EloquentCartRepository extends EloquentBaseRepository implements CartRepository {
    use CommonTrait;

    /**
     * @var \Modules\Order\Entities\Session
     */
    protected $session;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function __construct($model) {
        $this->session = new \Modules\Order\Entities\Session();

        parent::__construct($model);
    }

    /**
     * @param $user_id
     * @param $session_id
     * @param null $coupon
     * @return mixed
     */
    public function addSession($user_id, $session_id, $coupon = null) {
        $session = $this->session->newInstance()->where('user_id', $user_id)->where('session_id', $session_id)->first();
        if (!$session) {
            $session = new \Modules\Order\Entities\Session();
            $session->user_id = $user_id;
            $session->session_id = $session_id;
        }
        if (!is_null($coupon)) $session->coupon = $coupon;
        $session->save();

        return $session;
    }

    /**
     * @param $user_id
     * @param $session_id
     * @param $coupon
     * @return mixed
     */
    public function addCoupon($user_id, $session_id, $coupon) {
        $session = $this->session->newInstance()->where('user_id', $user_id)->where('session_id', $session_id)->first();
        if (!$session) {
            $session = new \Modules\Order\Entities\Session();
            $session->user_id = $user_id;
            $session->session_id = $session_id;
        }
        $session->coupon = $coupon;
        $session->save();

        return $session;
    }

    /**
     * @param $user_id
     * @param $session_id
     * @param $voucher
     * @return mixed
     */
    public function addVoucher($user_id, $session_id, $voucher) {
        $session = $this->session->newInstance()->where('user_id', $user_id)->where('session_id', $session_id)->first();
        if (!$session) {
            $session = new \Modules\Order\Entities\Session();
            $session->user_id = $user_id;
            $session->session_id = $session_id;
        }
        $session->voucher = $voucher;
        $session->save();

        return $session;
    }

    /**
     * @param $user_id
     * @param $session_id
     * @param $shipping_code
     * @param $shipping_fee
     * @param $shipping_discount
     * @return mixed
     */
    public function addShipping($user_id, $session_id, $shipping_code, $shipping_fee, $shipping_discount) {
        $session = $this->session->newInstance()->where('user_id', $user_id)->where('session_id', $session_id)->first();
        if (!$session) {
            $session = new \Modules\Order\Entities\Session();
            $session->user_id = $user_id;
            $session->session_id = $session_id;
        }
        $session->shipping_code = $shipping_code;
        $session->shipping_fee = $shipping_fee;
        $session->shipping_discount = $shipping_discount;
        $session->save();

        return $session;
    }

    /**
     * Get Products
     *
     * @param $user_id
     * @param $session_id
     * @param int $tz
     * @param boolean $refresh
     * @return array
     */
    public function getProducts($user_id, $session_id, $tz = -420, $refresh = false) {
        if ($refresh) $this->data = [];
        if (!$this->data) {
            $dateNow = $this->getDateLocalFromTz($tz);
            $fields = [
                'crt__carts.id', 'crt__carts.product_id', 'crt__carts.quantity', 'crt__carts.type',
                'pd.category_id as pd__category_id', 'pd.master_id', 'pd.gift_set_id', 'pd.model as pd__model', 'pd.name as pd__name',
                'pd.long_name as pd__long_name', 'pd.stock_status as pd__stock_status', 'pd.price as pd__price',
                'pd.coins as pd__coins', 'pd.quantity as pd__quantity', 'pd.weight as pd__weight', 'pd.length as pd__length',
                'pd.width as pd__width', 'pd.image as pd__image', 'pd.alias as pd__alias', 'pd.no_cod as pd__no_cod',
            ];
            $results = $this->getModel()->leftJoin('pd__products as pd', 'pd.id', '=', 'crt__carts.product_id');
            if ($user_id) {
                $results = $results->where('crt__carts.user_id', $user_id);
                // $fields[] = \DB::raw('(select count(*) from `pd__product_likes` where `product_id` = `crt__carts`.`product_id` and `user_id` = ' . $user_id . ' and `liked` = 1) as liked');
            }
            $results = $results->where('crt__carts.session_id', $session_id)/*->where('pd.status', 1)*/->where('crt__carts.quantity', '>', 0)->select($fields)->get();
            foreach ($results as $result) {
                $priceo = (float)$result->pd__price;
                $price = $priceo;
                $specialo = 0;
                $is_sale = false;
                $special_id = null;
                $special_price_total = null;
                if ($result->type != 'G') { // Gift (by coin)
                    // Product Specials
                    $product_special_query = ProductSpecial::where('product_id', $result->product_id)
                        //->where('user_group_id', $config_user_group_id)
                        ->whereRaw("((`start_date` is null or UNIX_TIMESTAMP(`start_date`) <= UNIX_TIMESTAMP('$dateNow')) and (`end_date` is null or UNIX_TIMESTAMP('$dateNow') <= UNIX_TIMESTAMP(`end_date`))) and (`quantity` is null or `quantity` >= $result->quantity)")
                        ->orderBy('priority', 'asc')->orderBy('price', 'asc')->select(['id', 'price', 'quantity', 'used'])->first();
                    if ($product_special_query) {
                        $specialo = (float)$product_special_query->price;
                        $price = $specialo;
                        $special_id = $product_special_query->id;
                        if (!is_null($product_special_query->quantity)) {
                            $is_sale = true;
                            if ($product_special_query->quantity < $result->quantity) {
                                $special_price_total = (float)($specialo * $product_special_query->quantity) +  $result->pd__price * ((int)$result->quantity - $product_special_query->quantity);
                            }
                        }
                    }
                }

                $cart_quantity = (int)$result->quantity;
                $product_quantity = (int)$result->pd__quantity;
                $product_coins = 0;
                if ($result->type == 'G') { // Gift (by coin)
                    $product_coins = (int)$result->pd__coins;
                    $price = 0;
                    $priceo = 0;
                }
                $this->data[] = array_merge($result->toArray(), [
                    'cart_id'    => $result->id,
                    'stock'      => $product_quantity < $cart_quantity ? false : true,
                    'stock_status' => $result->pd__stock_status,
                    'price'      => $price,
                    'total'      => $special_price_total ? $special_price_total : $price * $cart_quantity,
                    'coins'      => $product_coins,
                    'coin_total' => $product_coins * $cart_quantity,
                    'priceo'     => $priceo,
                    'specialo'   => $specialo,
                    'model'      => $result->pd__model,
                    'name'       => $result->pd__name,
                    'alias'      => $result->pd__alias,
                    'liked'      => $result->liked,
                    'weight'     => (float)$result->pd__weight * $cart_quantity,
                    'length'     => (float)$result->pd__length,
                    'width'      => (float)$result->pd__width,
                    'height'     => (float)$result->pd__height,
                    'gifts'      => '',
                    'is_sale'    => $is_sale,
                    'special_id' => $special_id,
                    'no_cod'     => $result->pd__no_cod,
                    'category_id'=> $result->pd__category_id,
                ]);
            }

        }
        return $this->data;
    }

    /**
     * Is valid cart
     * @param $user_id
     * @param $session_id
     * @return bool
     */
    public function isValidCart($user_id, $session_id) {
        $model = $this->getModel()->where('session_id', $session_id)->where('type', 'T');
        if ($user_id) $model = $model->where('user_id', $user_id);
        $model = $model->first();
        return $model ? true : false;
    }

    /**
     * Is valid product coin
     * @param $user_id
     * @param $session_id
     * @return bool
     */
    public function isValidProductCoin($user_id, $session_id) {
        $model = $this->getModel()->where('user_id', $user_id)->where('session_id', $session_id)->where('type', 'G')->where('quantity', '>', 1)->first();
        return $model ? true : false;
    }

    /**
     * Get Cart
     * @param $user_id
     * @param $session_id
     * @param $id
     * @return mixed
     */
    public function getCart($user_id, $session_id, $id) {
        return $this->getModel()->where('id', $id)->where('user_id', $user_id)->where('session_id', $session_id)->first();
    }

    /**
     * Get Cart By Product
     * @param $user_id
     * @param $session_id
     * @param $product_id
     * @return mixed
     */
    public function getCartByProduct($user_id, $session_id, $product_id) {
        return $this->getModel()->where('product_id', $product_id)->where('user_id', $user_id)->where('session_id', $session_id)->first();
    }

    /**
     * Add Cart
     *
     * @param $user_id
     * @param $session_id
     * @param $product_id
     * @param int $quantity
     * @param string $type (T:Trade (default), G:Gift (by coin), I:Included Products)
     */
    public function addCart($user_id, $session_id, $product_id, $quantity = 1, $type = 'T') {
        $total = $this->getModel()->where('user_id', $user_id)->where('session_id', $session_id)->where('product_id', $product_id)->where('type', $type)->count();
        if (!$total) {
            $this->create(['user_id' => $user_id, 'session_id' => $session_id, 'product_id' => $product_id, 'quantity' => $quantity, 'type' => $type]);
        } else {
            $this->getModel()->where('user_id', $user_id)->where('session_id', $session_id)->where('product_id', $product_id)->where('type', $type)->update(['quantity' => \DB::raw("(`quantity` + $quantity)")]);
        }
    }

    /**
     * Update Cart
     *
     * @param $user_id
     * @param $session_id
     * @param $id
     * @param $quantity
     */
    public function updateCart($user_id, $session_id, $id, $quantity) {
        $this->getModel()->where('id', $id)->where('user_id', $user_id)->where('session_id', $session_id)->update(['quantity' => $quantity]);
    }

    /**
     * Remove Cart
     *
     * @param $user_id
     * @param $session_id
     * @param $id
     */
    public function removeCart($user_id, $session_id, $id) {
        $this->getModel()->where('id', $id)->where('user_id', $user_id)->where('session_id', $session_id)->delete();
    }

    /**
     * Remove Product Cart
     *
     * @param $user_id
     * @param $session_id
     * @param $product_id
     */
    public function removeCartProduct($user_id, $session_id, $product_id) {
        $this->getModel()->where('product_id', $product_id)->where('user_id', $user_id)->where('session_id', $session_id)->delete();
    }

    /**
     * Clear Cart
     *
     * @param $user_id
     * @param $session_id
     */
    public function clearCart($user_id, $session_id) {
        $this->getModel()->where('user_id', $user_id)->where('session_id', $session_id)->delete();
        $this->session->newInstance()->where('user_id', $user_id)->where('session_id', $session_id)->delete();
        $this->data = [];
    }

    /**
     * Get Weight
     *
     * @param $user_id
     * @param $session_id
     * @param int $tz
     * @return int
     */
    public function getWeight($user_id, $session_id, $tz = -420) {
        $weight = 0;
        foreach ($this->getProducts($user_id, $session_id, $tz) as $product) {
            //if ($product['shipping']) {
            $weight += $product['weight'];
            //}
        }

        return $weight;
    }

    /**
     * Get Sub Total
     *
     * @param $user_id
     * @param $session_id
     * @param int $tz
     * @return int
     */
    public function getSubTotal($user_id, $session_id, $tz = -420) {
        $total = 0;
        foreach ($this->getProducts($user_id, $session_id, $tz) as $product) {
            $total += $product['total'];
        }
        return $total;
    }

    /**
     * Get Included Total
     *
     * @param $user_id
     * @param $session_id
     * @param int $tz
     * @return int
     */
    public function getIncludedTotal($user_id, $session_id, $tz = -420) {
        $included_total = 0;
        foreach ($this->getProducts($user_id, $session_id, $tz) as $product) {
            if ($product['type'] == 'I') $included_total += $product['total'];
        }
        return $included_total;
    }

    /**
     * Get Taxes
     *
     * @return array
     */
    public function getTaxes() {
        $tax_data = [];

        return $tax_data;
    }

//    /**
//     * Get total
//     *
//     * @param $user_id
//     * @param $session_id
//     * @param int $tz
//     * @return float|int
//     */
//    public function getTotal($user_id, $session_id, $tz = -420) {
//        $total = 0;
//        foreach ($this->getProducts($user_id, $session_id, $tz) as $product) {
//            $total += $product['price'] * $product['quantity'];
//        }
//
//        return $total;
//    }

    /**
     * Get coins
     *
     * @param $user_id
     * @param $session_id
     * @param int $tz
     * @return float|int
     */
    public function getCoins($user_id, $session_id, $tz = -420) {
        $coin_total = 0;
        foreach ($this->getProducts($user_id, $session_id, $tz) as $product) {
            $coin_total += $product['coins'] * $product['quantity'];
        }

        return $coin_total;
    }

    /**
     * Check coins
     *
     * @param $user_id
     * @param $session_id
     * @param int $product_id
     * @param int $quantity
     * @param int $tz
     * @return float|int
     */
    public function checkCoins($user_id, $session_id, $product_id = 0, $quantity = 0, $tz = -420) {
        $coin_total = 0;
        foreach ($this->getProducts($user_id, $session_id, $tz) as $product) {
            if ($product['product_id'] == $product_id) {
                if ($quantity) {
                    $product['quantity'] = $quantity;
                } else {
                    $product['quantity']++;
                }
            }
            $coin_total += $product['coins'] * $product['quantity'];
        }

        return $coin_total;
    }

    /**
     * Count tickets
     *
     * @param $user_id
     * @param $session_id
     * @param int $tz
     * @return int
     */
    public function countProducts($user_id, $session_id, $tz = -420) {
        $ticket_total = 0;
        $tickets = $this->getProducts($user_id, $session_id, $tz);
        foreach ($tickets as $ticket) {
            $ticket_total += $ticket['quantity'];
        }

        return $ticket_total;
    }

    /**
     * Has tickets
     *
     * @param $user_id
     * @param $session_id
     * @param int $tz
     * @return int
     */
    public function hasProducts($user_id, $session_id, $tz = -420) {
        return count($this->getProducts($user_id, $session_id, $tz));
    }

    /**
     * Has stock
     *
     * @param $user_id
     * @param $session_id
     * @param int $tz
     * @return bool
     */
    public function hasStock($user_id, $session_id, $tz = -420) {
        foreach ($this->getProducts($user_id, $session_id, $tz) as $item) {
            if (!$item['stock']) {
                return false;
            }
        }

        return true;
    }

    /*****************************************************
     ********************* Coupon *************************
     *****************************************************/
    /**
     * @param $coupon
     * @return mixed
     */
    protected function getTotalCouponHistoriesByCoupon($coupon) {
        return CouponHistory::leftJoin('mkt__coupons as c', 'c.id', 'mkt__coupon_histories.coupon_id')->where('c.code', $coupon)->count();
    }

    /**
     * @param $coupon
     * @param $user_id
     * @return mixed
     */
    protected function getTotalCouponHistoriesByUserId($coupon, $user_id) {
        return CouponHistory::leftJoin('mkt__coupons as c', 'c.id', 'mkt__coupon_histories.coupon_id')->where('c.code', $coupon)->where('mkt__coupon_histories.user_id', $user_id)->count();
    }

    private function getSession($user_id, $session_id) {
        $session = $this->session->newInstance()->where('session_id', $session_id);
        if ($user_id) $session = $session->where('user_id', $user_id);
        $session = $session->first();

        return $session;
    }

    /**
     * @param $session
     * @param int $tz
     * @return array|bool
     */
    protected function getCoupon($session, $tz = -420) {
        $code = $session && $session->coupon ? $session->coupon : false;
        if ($code) {
            $status = true;
            $dateNow = date('Y-m-d H:i:s');
            $coupon_info = Coupon::where('code', $code)->where('status', 1)
                ->whereRaw("((`start_date` is null or UNIX_TIMESTAMP(`start_date`) <= UNIX_TIMESTAMP('$dateNow')) and (`end_date` is null or UNIX_TIMESTAMP('$dateNow') <= UNIX_TIMESTAMP(`end_date`)))")
                ->first();
            $product_data = [];
            if ($coupon_info) {
                if ($coupon_info->total > $this->getSubTotal($session->user_id, $session->session_id, $tz)) {
                    $status = false;
                }
                /*$coupon_total = $this->getTotalCouponHistoriesByCoupon($code); // Lỗi khi có người hủy đơn. Check sau
                if ($coupon_info->uses_total > 0 && ($coupon_total > $coupon_info->uses_total)) {
                    $status = false;
                }*/
                /*if ($coupon_info->logged) {
                    $status = false;
                }*/
                if ($coupon_info->limited && $coupon_info->limited > 0 && $coupon_info->limited <= $coupon_info->uses_total) {
                    $status = false;
                }
                $user_total = $this->getTotalCouponHistoriesByUserId($code, $session->user_id);
                if ($coupon_info->uses_customer > 0 && ($user_total >= $coupon_info->uses_customer)) {
                    $status = false;
                }
                // Products
                /*$coupon_product_data = [];
                $coupon_products = CouponProduct::where('coupon_id', $coupon_info->id)->get();
                foreach ($coupon_products as $item) {
                    $coupon_product_data[] = $item->product_id;
                }*/
                // Categories
                /*$coupon_category_data = [];
                $coupon_categories = CouponCategory::leftJoin('pd__category_path as cp', 'mkt__coupon_categories.category_id', '=', 'cp.path_id')->where('mkt__coupon_categories.coupon_id', $coupon_info->id)->get();
                foreach ($coupon_categories as $item) {
                    $coupon_category_data[] = $item->category_id;
                }
                if ($coupon_product_data || $coupon_category_data) {
                    foreach ($this->getProducts($user_id, $session_id, $tz) as $product) {
                        if (in_array($product['product_id'], $coupon_product_data)) {
                            $product_data[] = (int)$product['product_id'];
                            continue;
                        }
                        foreach ($coupon_category_data as $category_id) {
                            $coupon_category = \Modules\Product\Entities\Product2Category::where('product_id', $product['product_id'])->where('category_id', $category_id)->count();
                            if ($coupon_category) {
                                $product_data[] = (int)$product['product_id'];
                                continue;
                            }
                        }
                    }
                    if (!$product_data) $status = false;
                }*/
            } else {
                $status = false;
            }

            if ($status) return array_merge($coupon_info->toArray(), ['product' => $product_data]);
        }

        return false;
    }

    /**
     * Get Coupon Total
     *
     * @param $session
     * @param $total
     * @param int $tz
     * @return array
     */
    protected function getCouponTotal($session, $total, $tz = -420) {
        $discount_code = '';
        $discount_total = 0;
        $coupon_info = $this->getCoupon($session, $tz);
        if ($coupon_info) {
            $discount_code = $coupon_info['code'];
            $discount_total = 0;
            $coupon_products = $coupon_info['product_ids'] ? explode(",", $coupon_info['product_ids']) : [];
            $coupon_categories = $coupon_info['category_ids'] ? explode(",", $coupon_info['category_ids']) : [];
            if (!$coupon_products && !$coupon_categories) {
                $sub_total = $this->getSubTotal($session->user_id, $session->session_id, $tz);
            } else {
                $sub_total = 0;
                foreach ($this->getProducts($session->user_id, $session->session_id, $tz) as $product) {
                    if($coupon_products) {
                        // COUPON EXIST PRODUCT IDS
                        $exist_product = in_array($product['product_id'], $coupon_products);
                        if ($coupon_info['rule'] === COUPON_RULE_INCLUDE) {
                            if ($exist_product) $sub_total += $product['total'];
                        } else if ($coupon_info['rule'] === COUPON_RULE_EXCLUDE) {
                            if (!$exist_product) $sub_total += $product['total'];
                        }
                    } else if ($coupon_categories) {
                        // COUPON EXIST CATEGORY IDS
                        $exist_category = in_array($product['category_id'], $coupon_categories);
                        if ($coupon_info['rule'] === COUPON_RULE_INCLUDE) {
                            if ($exist_category) $sub_total += $product['total'];
                        } else if ($coupon_info['rule'] === COUPON_RULE_EXCLUDE) {
                            if (!$exist_category) $sub_total += $product['total'];
                        }
                    }
                }
            }
            if ($coupon_info['group'] == 'shipping') {
                if ($coupon_info['type'] == 'F') {
                    $discount = min($coupon_info['discount'], $session->shipping_fee);
                } else if ($coupon_info['type'] == 'P') {
                    $discount = ($coupon_info['discount'] * $session->shipping_fee) / 100;
                }
                $remaining_shipping = $session->shipping_fee - $session->shipping_discount;
                $discount_total = min($discount, $remaining_shipping);
            } else {
                if ($coupon_info['type'] == 'F') $coupon_info['discount'] = min($coupon_info['discount'], $sub_total);
                foreach ($this->getProducts($session->user_id, $session->session_id, $tz) as $product) {
                    $discount = 0;
                    $status = true;
                    if ($coupon_info['rule'] === COUPON_RULE_INCLUDE && ($coupon_products || $coupon_categories)) {
                        // COUPON INCLUDE
                        $status = $coupon_products ? in_array($product['product_id'],  $coupon_products) : in_array($product['category_id'],  $coupon_categories);
                    } else if ($coupon_info['rule'] === COUPON_RULE_EXCLUDE && ($coupon_products || $coupon_categories)) {
                        // COUPON EXCLUDE
                        $status = $coupon_products ? !in_array($product['product_id'],  $coupon_products) : !in_array($product['category_id'],  $coupon_categories);
                    }
                    //if ($status && $product['type'] != 'I') {
                    if ($status) {
                        if ($coupon_info['type'] == 'F') {
                            $discount = $coupon_info['discount'] * ($product['total'] / $sub_total);
                        } else if ($coupon_info['type'] == 'P') {
                            $discount = $coupon_info['discount'] * ($product['total'] / 100);
                        }
                        /*if ($product['tax_class_id']) {
                            $tax_rates = $this->tax->getRates($product['total'] - ($product['total'] - $discount), $product['tax_class_id']);
                            foreach ($tax_rates as $tax_rate) {
                                if ($tax_rate['type'] == 'P') {
                                    $taxes[$tax_rate['tax_rate_id']] -= $tax_rate['amount'];
                                }
                            }
                        }*/
                    }
                    $discount_total += $discount;
                }
            }
            /*if ($coupon_info['shipping'] && isset($this->session->data['pd_shipping_method'])) {
                if (!empty($this->session->data['pd_shipping_method']['tax_class_id'])) {
                    $tax_rates = $this->tax->getRates($this->session->data['pd_shipping_method']['cost'], $this->session->data['pd_shipping_method']['tax_class_id']);
                    foreach ($tax_rates as $tax_rate) {
                        if ($tax_rate['type'] == 'P') {
                            $taxes[$tax_rate['tax_rate_id']] -= $tax_rate['amount'];
                        }
                    }
                }
                $discount_total += $this->session->data['pd_shipping_method']['cost'];
            }*/
            // If discount greater than total
            if ($discount_total > $total) $discount_total = $total;
            // If discount greater than max discount allow
            if ($coupon_info['type'] === 'P' && $coupon_info['max_discount'] > 0 && $discount_total > $coupon_info['max_discount']) $discount_total = $coupon_info['max_discount'];
        }

        return [$discount_code, $discount_total, $coupon_info];
    }

    /**
     * @param $session
     * @return array|bool
     */
    protected function getVoucher($session) {
        $code = $session && ($session->user_id || $session->session_id) && $session->voucher ? $session->voucher : false;
        if ($code) {
            $dateNow = date('Y-m-d H:i:s');
            return Voucher::where('user_id', $session->user_id)->where('code', $code)->where('quantity', '>', 0)
                ->whereRaw("((`start_date` is null or UNIX_TIMESTAMP('$dateNow') >= UNIX_TIMESTAMP(`start_date`)) and (`end_date` is null or UNIX_TIMESTAMP('$dateNow') <= UNIX_TIMESTAMP(`end_date`)))")
                ->first();
        }
        return false;
    }

    /**
     * Get Voucher Total
     *
     * @param $session
     * @param $total
     * @param int $tz
     * @return array
     */
    protected function getVoucherTotal($session, $total, $tz = -420) {
        $voucher_code = '';
        $voucher_total = 0;
        $voucher_info = $this->getVoucher($session);
        if ($voucher_info && $total >= $voucher_info['total']) {
            $sub_total = 0;
            $voucher_code = $voucher_info['code'];
            $voucher_products = $voucher_info['product_ids'] ? explode(",", $voucher_info['product_ids']) : [];
            $voucher_categories = $voucher_info['category_ids'] ? explode(",", $voucher_info['category_ids']) : [];
            // Cart products
            $cart_products = $this->getProducts($session->user_id, $session->session_id, $tz);

            if (!$voucher_products && !$voucher_categories) {
                $sub_total = $total;
            } else {
                foreach ($cart_products as $product) {
                    if ($voucher_products) {
                        // VOUCHER EXIST PRODUCT IDS
                        $exist_product = in_array($product['product_id'], $voucher_products);
                        if ($voucher_info['rule'] === COUPON_RULE_INCLUDE) {
                            if ($exist_product) $sub_total += $product['total'];
                        } else if ($voucher_info['rule'] === COUPON_RULE_EXCLUDE) {
                            if (!$exist_product) $sub_total += $product['total'];
                        }
                    } else if ($voucher_categories) {
                        // VOUCHER EXIST CATEGORY IDS
                        $exist_category = in_array($product['category_id'], $voucher_categories);
                        if ($voucher_info['rule'] === COUPON_RULE_INCLUDE) {
                            if ($exist_category) $sub_total += $product['total'];
                        } else if ($voucher_info['rule'] === COUPON_RULE_EXCLUDE) {
                            if (!$exist_category) $sub_total += $product['total'];
                        }
                    }
                }
            }
            if ($voucher_info['type'] == 'F') $voucher_info['amount'] = min($voucher_info['amount'], $sub_total);
            foreach ($cart_products as $product) {
                $discount = 0;
                $status = !$voucher_info['product_id'] ? true : $product['product_id'] !== $voucher_info['product_id'];
                if ($voucher_info['rule'] === COUPON_RULE_INCLUDE && ($voucher_products || $voucher_categories)) {
                    // VOUCHER INCLUDE
                    $status = $voucher_products ? in_array($product['product_id'],  $voucher_products) : in_array($product['category_id'],  $voucher_categories);
                } else if ($voucher_info['rule'] === COUPON_RULE_EXCLUDE && ($voucher_products || $voucher_categories)) {
                    // VOUCHER EXCLUDE
                    $status = $voucher_products ? !in_array($product['product_id'],  $voucher_products) : !in_array($product['category_id'],  $voucher_categories);
                }
                if ($status) {
                    if ($voucher_info['type'] == 'F') {
                        $discount = $voucher_info['amount'] * ($product['total'] / $sub_total);
                    } else if ($voucher_info['type'] == 'P') {
                        $discount = $voucher_info['amount'] * ($product['total'] / 100);
                    }
                }
                $voucher_total += $discount;
            }
            // If discount greater than total
            if ($voucher_total > $total) $voucher_total = $total;
            // If discount greater than max discount allow
            if ($voucher_info['type'] === 'P' && $voucher_info['max_discount'] > 0 && $voucher_total > $voucher_info['max_discount']) $voucher_total = $voucher_info['max_discount'];
        }

        return [$voucher_code, $voucher_total];
    }

    /**
     * Get Order Gifts
     *
     * @param $sub_total
     * @return array
     */
    public function getOrderGifts($sub_total) {
        $dateNow = date('Y-m-d H:i:s');
        $next_gifts = [];
        $current_gifts = [];
        $gift_orders_id = '';

        return [$next_gifts, $current_gifts, $gift_orders_id];
    }

    /**
     * Get Totals
     *
     * @param $user_id
     * @param $session_id
     * @param int $tz
     * @return array
     */
    public function getTotals($user_id, $session_id, $tz = -420) {
        $session = $this->getSession($user_id, $session_id);
        // Total
        $total = 0;
        // Sub total
        $sub_total = $this->getSubTotal($user_id, $session_id, $tz);
        $total += $sub_total;
        // Coupon
        list($discount_code, $discount_total, $coupon_info) = $this->getCouponTotal($session, $total, $tz);
        $total -= $discount_total;
        // Voucher
        list($voucher_code, $voucher_total) = $this->getVoucherTotal($session, $total, $tz);
        $total -= $voucher_total;
        $total = max(0, $total);
        // Shipping fee
        $shipping_code = '';
        $shipping_fee = 0;
        $shipping_discount = 0;
        if ($session) {
            $shipping_code = $session->shipping_code;
            $shipping_fee = $session->shipping_fee;
            $shipping_discount = $session->shipping_discount;
        }
        $shipping_total = $shipping_fee - $shipping_discount;
        $total += $shipping_total;
        // Vat
        $vat = 0;
        // Included product
        $included_total = $this->getIncludedTotal($user_id, $session_id, $tz);
        // Coupon info
        $coupon = null;
        if ($coupon_info) $coupon = ['id' => $coupon_info['id'], 'name' => $coupon_info['name'], 'code' => $coupon_info['code'], 'end_date' => $coupon_info['end_date']];

        list($next_gifts, $current_gifts, $gift_orders_id) = $this->getOrderGifts($sub_total);

        return [$sub_total, $discount_code, $discount_total, $voucher_code, $voucher_total, $shipping_code, $shipping_fee, $shipping_discount, $vat, $total, $included_total, $coupon, $next_gifts, $current_gifts, $gift_orders_id];
    }
}
