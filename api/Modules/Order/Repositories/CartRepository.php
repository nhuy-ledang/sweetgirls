<?php namespace Modules\Order\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface CartRepository extends BaseRepository {
    /**
     * @param $user_id
     * @param $session_id
     * @param null $coupon
     * @return mixed
     */
    public function addSession($user_id, $session_id, $coupon = null);

    /**
     * @param $user_id
     * @param $session_id
     * @param $coupon
     * @return mixed
     */
    public function addCoupon($user_id, $session_id, $coupon);

    /**
     * @param $user_id
     * @param $session_id
     * @param $voucher
     * @return mixed
     */
    public function addVoucher($user_id, $session_id, $voucher);

    /**
     * @param $user_id
     * @param $session_id
     * @param $shipping_code
     * @param $shipping_fee
     * @param $shipping_discount
     * @return mixed
     */
    public function addShipping($user_id, $session_id, $shipping_code, $shipping_fee, $shipping_discount);

    /**
     * Get Products
     *
     * @param $user_id
     * @param $session_id
     * @param int $tz
     * @param boolean $refresh
     * @return array
     */
    public function getProducts($user_id, $session_id, $tz = -420, $refresh = false);

    /**
     * Is valid cart
     * @param $user_id
     * @param $session_id
     * @return bool
     */
    public function isValidCart($user_id, $session_id);

    /**
     * Is valid product coin
     * @param $user_id
     * @param $session_id
     * @return bool
     */
    public function isValidProductCoin($user_id, $session_id);

    /**
     * Get Cart
     * @param $user_id
     * @param $session_id
     * @param $id
     * @return mixed
     */
    public function getCart($user_id, $session_id, $id);

    /**
     * Get Cart By Product
     * @param $user_id
     * @param $session_id
     * @param $product_id
     * @return mixed
     */
    public function getCartByProduct($user_id, $session_id, $product_id);

    /**
     * Add Cart
     *
     * @param $user_id
     * @param $session_id
     * @param $product_id
     * @param int $quantity
     * @param string $type (T:Trade (default), G:Gift (by coin), I:Included Products)
     */
    public function addCart($user_id, $session_id, $product_id, $quantity = 1, $type = 'T');

    /**
     * Update Cart
     *
     * @param $user_id
     * @param $session_id
     * @param $id
     * @param $quantity
     */
    public function updateCart($user_id, $session_id, $id, $quantity);

    /**
     * Remove Cart
     *
     * @param $user_id
     * @param $session_id
     * @param $id
     */
    public function removeCart($user_id, $session_id, $id);

    /**
     * Remove Product Cart
     *
     * @param $user_id
     * @param $session_id
     * @param $product_id
     */
    public function removeCartProduct($user_id, $session_id, $product_id);

    /**
     * Clear Cart
     *
     * @param $user_id
     * @param $session_id
     */
    public function clearCart($user_id, $session_id);

    /**
     * Get Weight
     *
     * @param $user_id
     * @param $session_id
     * @param int $tz
     * @return int
     */
    public function getWeight($user_id, $session_id, $tz = -420);

    /**
     * Get Sub Total
     *
     * @param $user_id
     * @param $session_id
     * @param int $tz
     * @return int
     */
    public function getSubTotal($user_id, $session_id, $tz = -420);

//    /**
//     * Get total
//     *
//     * @param $user_id
//     * @param $session_id
//     * @param int $tz
//     * @return float|int
//     */
//    public function getTotal($user_id, $session_id, $tz = -420);

    /**
     * Get coins
     *
     * @param $user_id
     * @param $session_id
     * @param int $tz
     * @return float|int
     */
    public function getCoins($user_id, $session_id, $tz = -420);

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
    public function checkCoins($user_id, $session_id, $product_id = 0, $quantity = 0, $tz = -420);

    /**
     * Count tickets
     *
     * @param $user_id
     * @param $session_id
     * @param int $tz
     * @return int
     */
    public function countProducts($user_id, $session_id, $tz = -420);

    /**
     * Has tickets
     *
     * @param $user_id
     * @param $session_id
     * @param int $tz
     * @return int
     */
    public function hasProducts($user_id, $session_id, $tz = -420);

    /**
     * Has stock
     *
     * @param $user_id
     * @param $session_id
     * @param int $tz
     * @return bool
     */
    public function hasStock($user_id, $session_id, $tz = -420);

    /**
     * Get Totals
     *
     * @param $user_id
     * @param $session_id
     * @param int $tz
     * @return array
     */
    public function getTotals($user_id, $session_id, $tz = -420);

    /**
     * Get included total
     *
     * @param $user_id
     * @param $session_id
     * @param int $tz
     * @return int
     */
    public function getIncludedTotal($user_id, $session_id, $tz = -420);
}
