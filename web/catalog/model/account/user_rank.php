<?php
class ModelAccountUserRank extends Model {
    private $table = DB_PREFIX . 'user__ranks';

	public function getUserRank($user_rank_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . $this->table . " WHERE `id` = '" . (int)$user_rank_id . "'");

		return $query->row;
	}

	public function getUserRanks() {
		$query = $this->db->query("SELECT * FROM " . $this->table . " WHERE `status` = '1' ORDER BY rank asc");

		return $query->rows;
	}
}
