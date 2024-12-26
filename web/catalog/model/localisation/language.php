<?php
class ModelLocalisationLanguage extends Model {
    protected $table = DB_PREFIX . 'core__languages';

    protected function getTransformer($row) {
        return $row;
    }

	public function getLanguage($language_id) {
		$query = $this->db->query("SELECT * FROM " . $this->table . " WHERE id = '" . (int)$language_id . "'");

        if ($query->num_rows) {
            return $this->getTransformer($query->row);
        } else {
            return false;
        }
	}

	public function getLanguages() {
        $data = false; //$this->cache->get('catalog.language');

		if (!$data) {
			$data = [];

			$query = $this->db->query("SELECT * FROM " . $this->table . " WHERE status = '1' ORDER BY sort_order, name");

            foreach ($query->rows as $row) {
                $data[] = $this->getTransformer($row);
            }

			$this->cache->set('catalog.language', $data);
		}

		return $data;
	}
}
