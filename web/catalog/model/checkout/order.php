<?php
class ModelCheckoutOrder extends Model {
    protected $table = DB_PREFIX . 'orders';
    protected $opr_table = DB_PREFIX . 'order__products';
    protected $oto_table = DB_PREFIX . 'order__totals';
    protected $ohi_table = DB_PREFIX . 'order__histories';

    protected function builder($data) {
        $builder = [];
        if (!empty($data['invoice_prefix'])) $builder[] = "invoice_prefix = '" . $this->db->escape((string)$data['invoice_prefix']) . "'";
        if (!empty($data['store_name'])) $builder[] = "store_name = '" . $this->db->escape((string)$data['store_name']) . "'";
        if (!empty($data['store_url'])) $builder[] = "store_url = '" . $this->db->escape((string)$data['store_url']) . "'";
        if (!empty($data['user_id'])) $builder[] = "user_id = '" . (int)$data['user_id'] . "'";
        if (!empty($data['user_group_id'])) $builder[] = "user_group_id = '" . (int)$data['user_group_id'] . "'";
        if (!empty($data['first_name'])) $builder[] = "first_name = '" . $this->db->escape((string)$data['first_name']) . "'";
        if (!empty($data['last_name'])) $builder[] = "last_name = '" . $this->db->escape((string)$data['last_name']) . "'";
        if (!empty($data['gender'])) $builder[] = "gender = '" . $this->db->escape((string)$data['gender']) . "'";
        if (!empty($data['email'])) $builder[] = "email = '" . $this->db->escape((string)$data['email']) . "'";
        if (!empty($data['phone_number'])) $builder[] = "phone_number = '" . $this->db->escape((string)$data['phone_number']) . "'";
        if (!empty($data['payment_method'])) $builder[] = "payment_method = '" . $this->db->escape((string)$data['payment_method']) . "'";
        if (!empty($data['payment_code'])) $builder[] = "payment_code = '" . $this->db->escape((string)$data['payment_code']) . "'";
        if (!empty($data['shipping_method'])) $builder[] = "shipping_method = '" . $this->db->escape((string)$data['shipping_method']) . "'";
        if (!empty($data['shipping_code'])) $builder[] = "shipping_code = '" . $this->db->escape((string)$data['shipping_code']) . "'";
        if (!empty($data['shipping_first_name'])) $builder[] = "shipping_first_name = '" . $this->db->escape((string)$data['shipping_first_name']) . "'";
        if (!empty($data['shipping_last_name'])) $builder[] = "shipping_last_name = '" . $this->db->escape((string)$data['shipping_last_name']) . "'";
        if (!empty($data['shipping_company'])) $builder[] = "shipping_company = '" . $this->db->escape((string)$data['shipping_company']) . "'";
        if (!empty($data['shipping_address_1'])) $builder[] = "shipping_address_1 = '" . $this->db->escape((string)$data['shipping_address_1']) . "'";
        if (!empty($data['shipping_address_2'])) $builder[] = "shipping_address_2 = '" . $this->db->escape((string)$data['shipping_address_2']) . "'";
        if (!empty($data['shipping_country'])) $builder[] = "shipping_country = '" . $this->db->escape((string)$data['shipping_country']) . "'";
        if (!empty($data['shipping_country_id'])) $builder[] = "shipping_country_id = '" . (int)$data['shipping_country_id'] . "'";
        if (!empty($data['shipping_province'])) $builder[] = "shipping_province = '" . $this->db->escape((string)$data['shipping_province']) . "'";
        if (!empty($data['shipping_province_id'])) $builder[] = "shipping_province_id = '" . (int)$data['shipping_province_id'] . "'";
        if (!empty($data['shipping_district'])) $builder[] = "shipping_district = '" . $this->db->escape((string)$data['shipping_district']) . "'";
        if (!empty($data['shipping_district_id'])) $builder[] = "shipping_district_id = '" . (int)$data['shipping_district_id'] . "'";
        if (!empty($data['shipping_ward'])) $builder[] = "shipping_ward = '" . $this->db->escape((string)$data['shipping_ward']) . "'";
        if (!empty($data['shipping_ward_id'])) $builder[] = "shipping_ward_id = '" . (int)$data['shipping_ward_id'] . "'";
        if (!empty($data['comment'])) $builder[] = "comment = '" . $this->db->escape((string)$data['comment']) . "'";
        if (!empty($data['total'])) $builder[] = "total = '" . (float)$data['total'] . "'";
        if (!empty($data['tracking'])) $builder[] = "tracking = '" . $this->db->escape((string)$data['tracking']) . "'";
        if (!empty($data['lang'])) $builder[] = "lang = '" . $this->db->escape((string)$data['lang']) . "'";
        if (!empty($data['currency_code'])) $builder[] = "currency_code = '" . $this->db->escape((string)$data['currency_code']) . "'";
        if (!empty($data['ip'])) $builder[] = "ip = '" . $this->db->escape((string)$data['ip']) . "'";
        if (!empty($data['forwarded_ip'])) $builder[] = "forwarded_ip = '" . $this->db->escape((string)$data['forwarded_ip']) . "'";
        if (!empty($data['user_agent'])) $builder[] = "user_agent = '" . $this->db->escape((string)$data['user_agent']) . "'";
        if (!empty($data['accept_language'])) $builder[] = "accept_language = '" . $this->db->escape((string)$data['accept_language']) . "'";
        if (!empty($data['is_invoice'])) $builder[] = "is_invoice = '" . (int)$data['is_invoice'] . "'";

        return $builder;
    }

    public function addOrder($data) {
        // Create model
        $builder = $this->builder($data);
        $builder[] = "created_at = '" . date('Y-m-d H:i:s') . "'";
        $builder[] = "updated_at = '" . date('Y-m-d H:i:s') . "'";

        $this->db->query("insert into `" . $this->table . "` set " . implode(',', $builder));

        $id = $this->db->getLastId();

        // Products
        if (isset($data['products'])) {
            foreach ($data['products'] as $product) {
                $this->db->query("insert into " . $this->opr_table . " set `order_id` = '" . (int)$id . "', `product_id` = '" . (int)$product['product_id'] . "', `name` = '" . $this->db->escape($product['name']) . "', `model` = '" . $this->db->escape($product['model']) . "', `quantity` = '" . (int)$product['quantity'] . "', `price` = '" . (float)$product['price'] . "', `total` = '" . (float)$product['total'] . "'");
            }
        }

        // Totals
        if (isset($data['totals'])) {
            $data['totals'] = array_values($data['totals']);
            foreach ($data['totals'] as $key => $total) {
                $this->db->query("insert into " . $this->oto_table . " set `order_id` = '" . (int)$id . "', `code` = '" . $this->db->escape($total['code']) . "', `title` = '" . $this->db->escape($total['title']) . "', `value` = '" . (float)$total['value'] . "', `sort_order` = '" . ($key + 1) . "'");
            }
        }

        return $id;
    }

    public function editOrder($id, $data) {
        // Void the order first
        $this->addOrderHistory($id, $data['status']);

        $builder = [];//$this->builder($data);
        if (isset($data['transaction_no'])) $builder[] = "`transaction_no` = '" . $this->db->escape($data['transaction_no']) . "'";
        if (isset($data['response_code'])) $builder[] = "`response_code` = '" . $this->db->escape($data['response_code']) . "'";
        if (!empty($data['payload'])) $builder[] = "`payload` = '" . $this->db->escape($data['payload']) . "'";
        if (!empty($data['summary'])) $builder[] = "`summary` = '" . $this->db->escape($data['summary']) . "'";
        if (!empty($data['payment_at'])) $builder[] = "`payment_at` = '" . $this->db->escape($data['payment_at']) . "'";
        $builder[] = "`status` = '" . $this->db->escape($data['status']) . "'";
        $builder[] = "`updated_at` = '" . date('Y-m-d H:i:s') . "'";

        $this->db->query("update `" . $this->table . "` set " . implode(',', $builder) . " where `id` = '" . (int)$id . "'");
    }

    public function deleteOrder($id) {
        $this->db->query("delete from `" . $this->table . "` where `id` = '" . (int)$id . "'");
        $this->db->query("delete from `" . $this->opr_table . "` where `order_id` = '" . (int)$id . "'");
        $this->db->query("delete from `" . $this->ohi_table . "` where `order_id` = '" . (int)$id . "'");
    }

    public function getOrder($id) {
        $order_query = $this->db->query("select * from `" . $this->table . "` o where o.id = '" . (int)$id . "'");
        if ($order_query->num_rows) {
            $row = $order_query->row;
            $row['org_total'] = $row['total'] ? $row['total'] : 0;
            $row['total'] = number_format($row['total'], 0, ',', '.');
            $row['org_shipping_fee'] = $row['shipping_fee'] ? $row['shipping_fee'] : 0;
            $row['shipping_fee'] = number_format($row['shipping_fee'], 0, ',', '.');
            $row['discount_total'] = number_format($row['discount_total'], 0, ',', '.');

            return $row;
        } else {
            return false;
        }
    }

    public function getOrderProducts($id) {
        $query = $this->db->query("select * from `" . $this->opr_table . "` where `order_id` = '" . (int)$id . "'");

        $rows = [];
        foreach ($query->rows as $row) {
            $row['price'] = number_format($row['price'], 0, ',', '.');
            $rows[] = $row;
        }

        return $query->rows;
    }

    public function addOrderHistory($id, $status, $comment = '', $notify = false) {
        $order_info = $this->getOrder($id);
        if ($order_info) {
            /*// If current order status is not processing or complete but new status is processing or complete then commence completing the order
            if (!in_array($order_info['order_status_id'], array_merge((array)$this->config->get('config_processing_status'), (array)$this->config->get('config_complete_status'))) && in_array($order_status_id, array_merge((array)$this->config->get('config_processing_status'), (array)$this->config->get('config_complete_status')))) {
                // Stock subtraction
                $order_products = $this->getOrderProducts($id);
                foreach ($order_products as $order_product) {
                    $this->db->query("update " . DB_PREFIX . "product set quantity = (quantity - " . (int)$order_product['quantity'] . ") where id = '" . (int)$order_product['product_id'] . "' AND subtract = '1'");

                    // Reduce the master product stock level if product is a variant
                    $this->db->query("update " . DB_PREFIX . "product set quantity = (quantity - " . (int)$order_product['quantity'] . ") where id = '" . (int)$order_product['master_id'] . "' AND subtract = '1'");

                    // If quantity = 0. stock_status_id = Out of stock --TNT96
                    $this->load->model('product/product');
                    $product_info = $this->model_product_product->getProduct($order_product['product_id']);
                    if ($product_info['quantity'] == 0) {
                        $this->db->query("update " . DB_PREFIX . "product set stock_status_id = 3 where id = '" . (int)$order_product['product_id'] . "' AND subtract = '1'");
                    }
                }
            }*/

            // Update the DB with the new statuses
            $this->db->query("update `" . $this->table . "` set `status` = '" . $this->db->escape($status) . "', `updated_at` = '" . date('Y-m-d H:i:s') . "' where `id` = '" . (int)$id . "'");
            $this->db->query("insert into `" . $this->ohi_table . "` set `order_id` = '" . (int)$id . "', `status` = '" . $this->db->escape($status) . "', `notify` = '" . (int)$notify . "', `comment` = '" . $this->db->escape($comment) . "', `created_at` = '" . date('Y-m-d H:i:s') . "'");

            $order_history_id = $this->db->getLastId();

            /*// If old order status is the processing or complete status but new status is not then commence restock, and remove coupon, voucher and reward history
            if (in_array($order_info['order_status_id'], array_merge((array)$this->config->get('config_processing_status'), (array)$this->config->get('config_complete_status'))) && !in_array($order_status_id, array_merge((array)$this->config->get('config_processing_status'), (array)$this->config->get('config_complete_status')))) {
                // Restock
                $order_products = $this->getOrderProducts($id);
                foreach ($order_products as $order_product) {
                    $this->db->query("update `" . DB_PREFIX . "product` set quantity = (quantity + " . (int)$order_product['quantity'] . ") where id = '" . (int)$order_product['product_id'] . "' AND subtract = '1'");

                    // Restock the master product stock level if product is a variant
                    $this->db->query("update " . DB_PREFIX . "product set quantity = (quantity + " . (int)$order_product['quantity'] . ") where id = '" . (int)$order_product['master_id'] . "' AND subtract = '1'");
                }
            }*/

            $this->cache->delete('product');

            return $order_history_id;
        }
    }
}
