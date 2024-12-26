<?php
class ModelTotalCoupon extends Model {
    protected $table = DB_PREFIX . 'mkt__coupons';
    //protected $product_table = DB_PREFIX . 'mkt__coupon_products';
    //protected $category_table = DB_PREFIX . 'mkt__coupon_categories';
    protected $history_table = DB_PREFIX . 'mkt__coupon_histories';
    protected $user_table = DB_PREFIX . 'users';
    protected $category_path_table = DB_PREFIX . 'pd__category_path';
    protected $product2category_table = DB_PREFIX . 'pd__product2category';

    protected $casts = [
        'id'            => 'integer',
        'total'         => 'double',
        'uses_total'    => 'integer',
        'uses_customer' => 'integer',
        'status'        => 'boolean',
    ];

    public function getCoupon($code) {
        $status = true;

        $coupon_query = $this->db->query("select * from `" . $this->table . "` where `code` = '" . $this->db->escape($code) . "' and ((`start_date` is null or `start_date` < NOW()) and (`end_date` is null or `end_date` > NOW())) and `status` = '1'");
        if ($coupon_query->num_rows) {
            if ($coupon_query->row['total'] > $this->cart->getSubTotal()) {
                $status = false;
            }

            $coupon_total = $this->getTotalCouponHistoriesByCoupon($code);
            if ($coupon_query->row['uses_total'] > 0 && ($coupon_total >= $coupon_query->row['uses_total'])) {
                $status = false;
            }

            /*if ($coupon_query->row['logged'] && !$this->user->getId()) {
                $status = false;
            }

            if ($this->user->getId()) {
                $user_total = $this->getTotalCouponHistoriesByUserId($code, $this->user->getId());
                if ($coupon_query->row['uses_customer'] > 0 && ($user_total >= $coupon_query->row['uses_customer'])) {
                    $status = false;
                }
            }*/

            /*// Products
            $coupon_product_data = [];
            $coupon_product_query = $this->db->query("select * from `" . $this->product_table . "` where coupon_id = '" . (int)$coupon_query->row['id'] . "'");
            foreach ($coupon_product_query->rows as $product) {
                $coupon_product_data[] = $product['product_id'];
            }*/

            /*// Categories
            $coupon_category_data = [];
            $coupon_category_query = $this->db->query("select * from `" . $this->category_table . "` cc left join `" . $this->category_path_table . "` cp ON (cc.category_id = cp.path_id) where cc.coupon_id = '" . (int)$coupon_query->row['id'] . "'");
            foreach ($coupon_category_query->rows as $category) {
                $coupon_category_data[] = $category['category_id'];
            }*/

            $product_data = [];
            /*if ($coupon_product_data || $coupon_category_data) {
                foreach ($this->cart->getProducts() as $product) {
                    if (in_array($product['product_id'], $coupon_product_data)) {
                        $product_data[] = (int)$product['product_id'];
                        continue;
                    }
                    foreach ($coupon_category_data as $category_id) {
                        $coupon_category_query = $this->db->query("select count(*) as total from `" . $this->product2category_table . "` where `product_id` = '" . (int)$product['product_id'] . "' and `category_id` = '" . (int)$category_id . "'");
                        if ($coupon_category_query->row['total']) {
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

        if ($status) {
            return array_merge($this->transform($coupon_query->row), [
                'product' => $product_data,
            ]);
        }
    }

    public function getTotal(&$totals, &$taxes, &$total) {
        if (isset($this->session->data['coupon'])) {
            $this->load->language('extension/total/coupon');
            $coupon_info = $this->getCoupon($this->session->data['coupon']);
            if ($coupon_info) {
                $discount_total = 0;
                if (!$coupon_info['product']) {
                    $sub_total = $this->cart->getSubTotal();
                } else {
                    $sub_total = 0;
                    foreach ($this->cart->getProducts() as $product) {
                        if (in_array($product['product_id'], $coupon_info['product'])) {
                            $sub_total += $product['total'];
                        }
                    }
                }
                if ($coupon_info['type'] == 'F') $coupon_info['discount'] = min($coupon_info['discount'], $sub_total);
                foreach ($this->cart->getProducts() as $product) {
                    $discount = 0;
                    $status = !$coupon_info['product'] ? true : in_array($product['product_id'], $coupon_info['product']);
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

                /*if ($coupon_info['shipping'] && isset($this->session->data['shipping_method'])) {
                    if (!empty($this->session->data['shipping_method']['tax_class_id'])) {
                        $tax_rates = $this->tax->getRates($this->session->data['shipping_method']['cost'], $this->session->data['shipping_method']['tax_class_id']);

                        foreach ($tax_rates as $tax_rate) {
                            if ($tax_rate['type'] == 'P') {
                                $taxes[$tax_rate['tax_rate_id']] -= $tax_rate['amount'];
                            }
                        }
                    }

                    $discount_total += $this->session->data['shipping_method']['cost'];
                }*/

                // If discount greater than total
                if ($discount_total > $total) {
                    $discount_total = $total;
                }

                if ($discount_total > 0) {
                    $totals['coupon'] = [
                        'code'  => 'coupon',
                        'title' => sprintf($this->language->get('text_coupon'), $this->session->data['coupon']),
                        'value' => -$discount_total,
                    ];

                    $total -= $discount_total;
                }
            }
        }
    }

    public function confirm($order_info, $order_total) {
        $code = '';

        $start = strpos($order_total['title'], '(') + 1;
        $end = strrpos($order_total['title'], ')');

        if ($start && $end) {
            $code = substr($order_total['title'], $start, $end - $start);
        }

        if ($code) {
            $status = true;

            $coupon_query = $this->db->query("select * from `" . $this->table . "` where `code` = '" . $this->db->escape($code) . "' and `status` = '1'");

            if ($coupon_query->num_rows) {
                $coupon_total = $this->getTotalCouponHistoriesByCoupon($code);

                if ($coupon_query->row['uses_total'] > 0 && ($coupon_total >= $coupon_query->row['uses_total'])) {
                    $status = false;
                }

                if ($order_info['user_id']) {
                    $user_total = $this->getTotalCouponHistoriesByUserId($code, $order_info['user_id']);

                    if ($coupon_query->row['uses_customer'] > 0 && ($user_total >= $coupon_query->row['uses_customer'])) {
                        $status = false;
                    }
                }
            } else {
                $status = false;
            }

            if ($status) {
                $this->db->query("insert into `" . $this->history_table . "` set coupon_id = '" . (int)$coupon_query->row['id'] . "', order_id = '" . (int)$order_info['order_id'] . "', user_id = '" . (int)$order_info['user_id'] . "', amount = '" . (float)$order_total['value'] . "', date_added = NOW()");
            } else {
                return 0;//$this->config->get('config_fraud_status_id');
            }
        }
    }

    public function unconfirm($order_id) {
        $this->db->query("delete from `" . $this->history_table . "` where order_id = '" . (int)$order_id . "'");
    }

    public function getTotalCouponHistoriesByCoupon($coupon) {
        $query = $this->db->query("select count(*) as total from `" . $this->history_table . "` ch left join `" . $this->table . "` c ON (ch.coupon_id = c.id) where c.code = '" . $this->db->escape($coupon) . "'");

        return $query->row['total'];
    }

    public function getTotalCouponHistoriesByUserId($coupon, $user_id) {
        $query = $this->db->query("select count(*) as total from `" . $this->history_table . "` ch left join `" . $this->table . "` c ON (ch.coupon_id = c.id) where c.code = '" . $this->db->escape($coupon) . "' and ch.user_id = '" . (int)$user_id . "'");

        return $query->row['total'];
    }
}
