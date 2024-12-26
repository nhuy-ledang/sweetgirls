<?php
class ModelLocalisationCurrency extends Model {
    private $table = DB_PREFIX . 'currency';

	public function getCurrencyByCode($currency) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . $this->table . " WHERE code = '" . $this->db->escape($currency) . "'");

		return $query->row;
	}

	public function getCurrencies() {
		$currency_data = $this->cache->get('currency');

		if (!$currency_data) {
			$currency_data = array();

			$query = $this->db->query("SELECT * FROM " . $this->table . " ORDER BY title ASC");

            foreach ($query->rows as $result) {
                $currency_data[$result['code']] = array(
                    'id'            => $result['id'],
                    'title'         => $result['title'],
                    'code'          => $result['code'],
                    'symbol_left'   => $result['symbol_left'],
                    'symbol_right'  => $result['symbol_right'],
                    'decimal_place' => $result['decimal_place'],
                    'value'         => $result['value'],
                    'status'        => $result['status'],
                    'date_modified' => $result['date_modified']
                );
            }

			$this->cache->set('currency', $currency_data);
		}

		return $currency_data;
	}
}