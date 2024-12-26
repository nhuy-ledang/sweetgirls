<?php
class ModelToolOnline extends Model {
	public function addOnline($ip, $user_id, $url, $referer) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "user__online` WHERE created_at < '" . date('Y-m-d H:i:s', strtotime('-1 hour')) . "'");

		$this->db->query("REPLACE INTO `" . DB_PREFIX . "user__online` SET `ip` = '" . $this->db->escape($ip) . "', `user_id` = '" . (int)$user_id . "', `url` = '" . $this->db->escape($url) . "', `referer` = '" . $this->db->escape($referer) . "', `created_at` = '" . $this->db->escape(date('Y-m-d H:i:s')) . "'");
	}
}
