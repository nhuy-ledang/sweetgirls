<?php
class ModelSettingModule extends Model {
    private $table = DB_PREFIX . 'module';

    public function getModule($id) {
        $query = $this->db->query("SELECT * FROM " . $this->table . " WHERE id = '" . (int)$id . "'");

        if ($query->row) {
            $result = json_decode($query->row['setting'], true);
            $result['alias'] = $query->row['alias'];
            return $result;
        } else {
            return array();
        }
    }

    public function getModuleByAlias($alias) {
        $query = $this->db->query("SELECT * FROM " . $this->table . " WHERE `alias` LIKE '" . $this->db->escape($alias) . "'");

        if ($query->row) {
            return json_decode($query->row['setting'], true);
        } else {
            return array();
        }
    }
}