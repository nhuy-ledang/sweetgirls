<?php
class ModelDesignSeoRegex extends Model {
    private $table = DB_PREFIX . 'seo__regex';

    public function getSeoRegexes() {
        $query = $this->db->query("SELECT * FROM `" . $this->table . "` ORDER BY `sort_order` ASC");

        return $query->rows;
    }
}