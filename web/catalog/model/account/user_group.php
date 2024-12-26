<?php
class ModelAccountUserGroup extends Model {
    private $table = DB_PREFIX . 'user__group';

	public function getUserGroup($user_group_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . $this->table . " cg WHERE cg.user_group_id = '" . (int)$user_group_id . "'");

		return $query->row;
	}

	public function getUserGroups() {
		$query = $this->db->query("SELECT * FROM " . $this->table . " cg ORDER BY cg.sort_order ASC, cg.name ASC");

		return $query->rows;
	}
}