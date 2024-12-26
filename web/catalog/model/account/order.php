<?php
class ModelAccountOrder extends Model {
	public function getOrder($order_id) {
        $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "orders` WHERE id = '" . (int)$order_id . "' AND user_id = '" . (int)$this->user->getId() . "'");

        if ($order_query->row) {
            return $order_query->row;
        } else {
            return false;
        }
	}

	public function getOrders() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "orders WHERE user_id = '" . (int)$this->user->getId() . "'");

		return $query->rows;
	}

	public function getProduct($order_id, $order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order__products WHERE order_id = '" . (int)$order_id . "' AND id = '" . (int)$order_product_id . "'");

		return $query->row;
	}

	public function getProducts($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order__products WHERE order_id = '" . (int)$order_id . "'");

		return $query->rows;
	}

	public function getOptions($order_id, $order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order__option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");

		return $query->rows;
	}

	public function getVouchers($order_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order__voucher` WHERE order_id = '" . (int)$order_id . "'");

		return $query->rows;
	}

	public function getTotals($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order__total WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order");

		return $query->rows;
	}

	public function getHistories($order_id) {
		$query = $this->db->query("SELECT created_at, os.name AS status, oh.comment, oh.notify FROM " . DB_PREFIX . "order__history oh LEFT JOIN " . DB_PREFIX . "order__status os ON oh.order_status_id = os.id WHERE oh.order_id = '" . (int)$order_id . "' ORDER BY oh.created_at");

		return $query->rows;
	}

	public function getTotalOrders() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` o WHERE user_id = '" . (int)$this->user->getId() . "' AND o.order_status_id > '0'");

		return $query->row['total'];
	}

	public function getTotalProductsByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order__product WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}

	public function getTotalVouchersByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order__voucher` WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}
}
