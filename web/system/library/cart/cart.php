<?php namespace Cart;
class Cart {
    private $table = DB_PREFIX . 'crt__carts';
    private $session_table = DB_PREFIX . 'crt__sessions';
    private $product_table = DB_PREFIX . 'pd__products';
    private $data = [];

    public function __construct($registry) {
        $this->config = $registry->get('config');
        $this->user = $registry->get('user');
        $this->session = $registry->get('session');
        $this->db = $registry->get('db');
        //$this->tax = $registry->get('tax');

        // Remove all the expired carts with no user ID
        $this->db->query("delete from " . $this->table . " where user_id = '0' and created_at < DATE_SUB('" . date('Y-m-d H:i:s') . "', INTERVAL 1 HOUR)");

        if ($this->user->getId()) {
            // We want to change the session ID on all the old items in the users cart
            $this->db->query("update " . $this->table . " set session_id = '" . $this->db->escape($this->session->getId()) . "' where user_id = '" . (int)$this->user->getId() . "'");
            $this->db->query("update " . $this->table . " set user_id = '" . (int)$this->user->getId() . "' where session_id = '" . $this->db->escape($this->session->getId()) . "'");
            $this->addSession();

            // Once the user is logged in we want to update the users cart
            $cart_query = $this->db->query("select * from " . $this->table . " where user_id = '0' and session_id = '" . $this->db->escape($this->session->getId()) . "'");

            foreach ($cart_query->rows as $cart) {
                $this->db->query("delete from " . $this->table . " where id = '" . (int)$cart['id'] . "'");

                // The advantage of using $this->add is that it will check if the products already exist and increase the quantity if necessary.
                $this->add($cart['product_id'], $cart['quantity']);
            }
        }
    }

    /**
     * @param null $coupon
     * @return mixed
     */
    public function addSession($coupon = null) {
        $query = $this->db->query("select * from `" . $this->session_table . "` where session_id = '" . $this->db->escape($this->session->getId()) . "'");
        if (!$query->num_rows) {
            $this->db->query("insert into `" . $this->session_table . "` set session_id = '" . $this->db->escape($this->session->getId()) . "', created_at = '" . date('Y-m-d H:i:s') . "'");
        }
        $builder = [];
        if (!is_null($coupon)) $builder[] = "coupon = '" . $this->db->escape($coupon) . "'";
        $builder[] = "updated_at = '" . date('Y-m-d H:i:s') . "'";
        if ((int)$this->user->getId()) {
            $this->db->query("delete from " . $this->session_table . " where user_id = '" . (int)$this->user->getId() . "' and session_id <> '" . $this->db->escape($this->session->getId()) . "'");
            $builder[] = "user_id = '" . (int)$this->user->getId() . "'";
        }
        $this->db->query("update " . $this->session_table . " set " . implode(', ', $builder) . " where session_id = '" . $this->db->escape($this->session->getId()) . "'");
    }

    public function getProducts() {
        if (!$this->data) {
            $cart_query = $this->db->query("select * from " . $this->table . " where user_id = '" . (int)$this->user->getId() . "' or session_id = '" . $this->db->escape($this->session->getId()) . "' and `type` <> 'I'");

            foreach ($cart_query->rows as $cart) {
                $stock = true;
                $product_query = $this->db->query("select * from " . $this->product_table . " p where p.id = '" . (int)$cart['product_id'] . "' /*and p.date_available <= NOW()*/ and (p.status = '1' or (p.stock_status <> 'out_of_stock'))");
                if ($product_query->num_rows && ($cart['quantity'] > 0)) {
                    $option_price = 0;
                    $option_points = 0;
                    $option_weight = 0;
                    $option_data = [];
                    $product_options = (array)json_decode($cart['option'], true);
                    if ($product_options) foreach ($product_options as $product_option_id => $value) {
                        $option_query = $this->db->query("SELECT po.id, po.option_id, o.name, o.type FROM " . DB_PREFIX . "pd__product_options po LEFT JOIN `" . DB_PREFIX . "pd__options` o ON (po.option_id = o.id) WHERE po.option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$cart['product_id'] . "'");

                        if ($option_query->num_rows) {
                            if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio') {
//                                $option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
                                $option_value_query = $this->db->query("SELECT ov.* FROM " . DB_PREFIX . "pd__option_values ov WHERE ov.id = '" . $value . "'");
                                if (!isset($option_value_query->row['price_prefix'])) $option_value_query->row['price_prefix'] = '';
                                if (!isset($option_value_query->row['points_prefix'])) $option_value_query->row['points_prefix'] = '';
                                if (!isset($option_value_query->row['weight_prefix'])) $option_value_query->row['weight_prefix'] = '';
                                if (!isset($option_value_query->row['subtract'])) $option_value_query->row['subtract'] = '';
                                if (!isset($option_value_query->row['quantity'])) $option_value_query->row['quantity'] = '';
                                if (!isset($option_value_query->row['price'])) $option_value_query->row['price'] = '';
                                if (!isset($option_value_query->row['points'])) $option_value_query->row['points'] = '';
                                if (!isset($option_value_query->row['weight'])) $option_value_query->row['weight'] = '';

                                if ($option_value_query->num_rows) {
                                    if ($option_value_query->row['price_prefix'] == '+') {
                                        $option_price += $option_value_query->row['price'];
                                    } else if ($option_value_query->row['price_prefix'] == '-') {
                                        $option_price -= $option_value_query->row['price'];
                                    }

                                    if ($option_value_query->row['points_prefix'] == '+') {
                                        $option_points += $option_value_query->row['points'];
                                    } else if ($option_value_query->row['points_prefix'] == '-') {
                                        $option_points -= $option_value_query->row['points'];
                                    }

                                    if ($option_value_query->row['weight_prefix'] == '+') {
                                        $option_weight += $option_value_query->row['weight'];
                                    } else if ($option_value_query->row['weight_prefix'] == '-') {
                                        $option_weight -= $option_value_query->row['weight'];
                                    }

                                    if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $cart['quantity']))) {
                                        $stock = false;
                                    }

                                    $option_data[] = [
                                        'product_option_id'       => $product_option_id,
                                        'product_option_value_id' => $value,
                                        'option_id'               => $option_query->row['option_id'],
                                        'id'                      => $option_value_query->row['id'],
                                        'name'                    => $option_query->row['name'],
                                        'value'                   => $option_value_query->row['name'],
                                        'type'                    => $option_query->row['type'],
                                        'quantity'                => $option_value_query->row['quantity'],
                                        'subtract'                => $option_value_query->row['subtract'],
                                        'price'                   => $option_value_query->row['price'],
                                        'price_prefix'            => $option_value_query->row['price_prefix'],
                                        'points'                  => $option_value_query->row['points'],
                                        'points_prefix'           => $option_value_query->row['points_prefix'],
                                        'weight'                  => $option_value_query->row['weight'],
                                        'weight_prefix'           => $option_value_query->row['weight_prefix'],
                                    ];
                                }
                            } else if ($option_query->row['type'] == 'checkbox' && is_array($value)) {
                                $option_item = [];

                                foreach ($value as $product_option_value_id) {
//                                    $option_value_query = $this->db->query("SELECT pov.option_value_id, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix, ovd.name FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (pov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
                                    $option_value_query = $this->db->query("SELECT ov.* FROM " . DB_PREFIX . "pd__option_values ov WHERE ov.id = '" . $product_option_value_id . "'");
                                    if (!isset($option_value_query->row['price_prefix'])) $option_value_query->row['price_prefix'] = '';
                                    if (!isset($option_value_query->row['points_prefix'])) $option_value_query->row['points_prefix'] = '';
                                    if (!isset($option_value_query->row['weight_prefix'])) $option_value_query->row['weight_prefix'] = '';
                                    if (!isset($option_value_query->row['subtract'])) $option_value_query->row['subtract'] = '';
                                    if (!isset($option_value_query->row['quantity'])) $option_value_query->row['quantity'] = '';
                                    if (!isset($option_value_query->row['price'])) $option_value_query->row['price'] = '';
                                    if (!isset($option_value_query->row['points'])) $option_value_query->row['points'] = '';
                                    if (!isset($option_value_query->row['weight'])) $option_value_query->row['weight'] = '';

                                    if ($option_value_query->num_rows) {
                                        if ($option_value_query->row['price_prefix'] == '+') {
                                            $option_price += $option_value_query->row['price'];
                                        } else if ($option_value_query->row['price_prefix'] == '-') {
                                            $option_price -= $option_value_query->row['price'];
                                        }

                                        if ($option_value_query->row['points_prefix'] == '+') {
                                            $option_points += $option_value_query->row['points'];
                                        } else if ($option_value_query->row['points_prefix'] == '-') {
                                            $option_points -= $option_value_query->row['points'];
                                        }

                                        if ($option_value_query->row['weight_prefix'] == '+') {
                                            $option_weight += $option_value_query->row['weight'];
                                        } else if ($option_value_query->row['weight_prefix'] == '-') {
                                            $option_weight -= $option_value_query->row['weight'];
                                        }

                                        if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $cart['quantity']))) {
                                            $stock = false;
                                        }

                                        $option_item[] = [
                                            'product_option_id'       => $product_option_id,
                                            'product_option_value_id' => $product_option_value_id,
                                            'option_id'               => $option_query->row['option_id'],
                                            'id'                      => $option_value_query->row['id'],
                                            'name'                    => $option_query->row['name'],
                                            'value'                   => $option_value_query->row['name'],
                                            'type'                    => $option_query->row['type'],
                                            'quantity'                => $option_value_query->row['quantity'],
                                            'subtract'                => $option_value_query->row['subtract'],
                                            'price'                   => $option_value_query->row['price'],
                                            'price_prefix'            => $option_value_query->row['price_prefix'],
                                            'points'                  => $option_value_query->row['points'],
                                            'points_prefix'           => $option_value_query->row['points_prefix'],
                                            'weight'                  => $option_value_query->row['weight'],
                                            'weight_prefix'           => $option_value_query->row['weight_prefix'],
                                        ];
                                    }
                                }
                                // Merge value checkbox
                                $value_name = '';
                                foreach ($option_item as $key => $item) {
                                    $value_name .= $item['value'];
                                    if ($key !== array_key_last($option_item)) {
                                        $value_name .= ', ';
                                    }
                                }
                                $option_data[] = [
                                    'name'  => $option_query->row['name'],
                                    'value' => $value_name,

                                ];

                            } else if ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
                                $option_data[] = [
                                    'product_option_id'       => $product_option_id,
                                    'product_option_value_id' => '',
                                    'option_id'               => $option_query->row['option_id'],
                                    'id'                      => '',
                                    'name'                    => $option_query->row['name'],
                                    'value'                   => $value,
                                    'type'                    => $option_query->row['type'],
                                    'quantity'                => '',
                                    'subtract'                => '',
                                    'price'                   => '',
                                    'price_prefix'            => '',
                                    'points'                  => '',
                                    'points_prefix'           => '',
                                    'weight'                  => '',
                                    'weight_prefix'           => '',
                                ];
                            }
                        }
                    }

                    $price = (float)$product_query->row['price'];

                    // Product Discounts
                    $discount_quantity = 0;
                    foreach ($cart_query->rows as $cart_2) {
                        if ($cart_2['product_id'] == $cart['product_id']) {
                            $discount_quantity += (int)$cart_2['quantity'];
                        }
                    }
                    // Stock
                    if (!$product_query->row['quantity'] || ($product_query->row['quantity'] < $cart['quantity'])) {
                        $stock = false;
                    }
                    $quantity = (int)$cart['quantity'];
                    $coins = (int)$product_query->row['coins'];
                    $row = array_merge($product_query->row, [
                        'cart_id'     => (int)$cart['id'],
                        'product_id'  => (int)$product_query->row['id'],
                        'quantity'    => $quantity,
                        'stock'       => $stock,
                        'price'       => $price,
                        'total'       => $price * $quantity,
                        'coins'       => $coins,
                        'coin_total'  => $coins * $quantity,
                        'org_price'   => (float)$product_query->row['price'],
                        'options'     => $option_data,
                    ]);
                    if (isset($row['image'])) $row['thumb_url'] = media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8'), 'thumb');

                    $this->data[] = $row;
                } else {
                    $this->remove($cart['id']);
                }
            }
        }

        return $this->data;
    }

    public function add($product_id, $quantity = 1, $option = []) {
        $query = $this->db->query("select count(*) as total from " . $this->table . " where user_id = '" . (int)$this->user->getId() . "' and session_id = '" . $this->db->escape($this->session->getId()) . "' and product_id = '" . (int)$product_id . "' and `option` = '" . $this->db->escape(json_encode($option)) . "'");
        if (!$query->row['total']) {
            $sql = "insert into `" . $this->table . "` set user_id = '" . (int)$this->user->getId() . "', session_id = '" . $this->db->escape($this->session->getId()) . "', product_id = '" . (int)$product_id . "', `option` = '" . $this->db->escape(json_encode($option)) . "', quantity = '" . (int)$quantity . "', created_at = '" . date('Y-m-d H:i:s') . "'";
        } else {
            $sql = "update `" . $this->table . "` set quantity = (quantity + " . (int)$quantity . ") where user_id = '" . (int)$this->user->getId() . "' and session_id = '" . $this->db->escape($this->session->getId()) . "' and product_id = '" . (int)$product_id . "' and `option` = '" . $this->db->escape(json_encode($option)) . "'";

        }
        $this->db->query($sql);

        $this->data = [];
    }

    public function update($id, $quantity) {
        $this->db->query("update " . $this->table . " set quantity = '" . (int)$quantity . "' where id = '" . (int)$id . "' and user_id = '" . (int)$this->user->getId() . "' and session_id = '" . $this->db->escape($this->session->getId()) . "'");

        $this->data = [];
    }

    public function remove($id) {
        $this->db->query("delete from " . $this->table . " where id = '" . (int)$id . "' and user_id = '" . (int)$this->user->getId() . "' and session_id = '" . $this->db->escape($this->session->getId()) . "'");

        $this->data = [];
    }

    public function clear() {
        $this->db->query("delete from " . $this->table . " where user_id = '" . (int)$this->user->getId() . "' or session_id = '" . $this->db->escape($this->session->getId()) . "'");
        $this->db->query("delete from " . $this->session_table . " where user_id = '" . (int)$this->user->getId() . "' or session_id = '" . $this->db->escape($this->session->getId()) . "'");

        $this->data = [];
    }

    public function getSubTotal() {
        $total = 0;

        foreach ($this->getProducts() as $product) {
            $total += $product['total'];
        }

        return $total;
    }

    public function getTaxes() {
        $tax_data = [];

        /*foreach ($this->getProducts() as $product) {
            if ($product['tax_class_id']) {
                $tax_rates = $this->tax->getRates($product['price'], $product['tax_class_id']);

                foreach ($tax_rates as $tax_rate) {
                    if (!isset($tax_data[$tax_rate['tax_rate_id']])) {
                        $tax_data[$tax_rate['tax_rate_id']] = ($tax_rate['amount'] * $product['quantity']);
                    } else {
                        $tax_data[$tax_rate['tax_rate_id']] += ($tax_rate['amount'] * $product['quantity']);
                    }
                }
            }
        }*/

        return $tax_data;
    }

    public function getTotal() {
        $total = 0;
        foreach ($this->getProducts() as $product) {
            //$total += $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];
            $total += $product['price'] * $product['quantity'];
        }

        return $total;
    }

    public function getCoins() {
        $coin_total = 0;
        foreach ($this->getProducts() as $product) {
            $coin_total += $product['coins'] * $product['quantity'];
        }

        return $coin_total;
    }

    public function countProducts() {
        $product_total = 0;
        $products = $this->getProducts();
        foreach ($products as $product) {
            $product_total += $product['quantity'];
        }

        return $product_total;
    }

    public function hasProducts() {
        return count($this->getProducts());
    }

    public function hasStock() {
        foreach ($this->getProducts() as $product) {
            if (!$product['stock']) {
                return false;
            }
        }

        return true;
    }

    public function hasShipping() {
        return true;
        /*foreach ($this->getProducts() as $product) {
            if ($product['shipping']) {
                return true;
            }
        }

        return false;*/
    }
}
