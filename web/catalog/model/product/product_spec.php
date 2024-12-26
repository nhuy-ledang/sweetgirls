<?php
class ModelProductProductSpec extends Model {
    protected $table = DB_PREFIX . 'pd__product_specs';
    protected $desc_table = DB_PREFIX . 'pd__product_spec_desc';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['translates', ' sort_order'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'integer',
        'product_id' => 'integer',
    ];

    protected function getTransformer($row) {
        return $this->transform($row);
    }

    public function getSpecs($product_id) {
        $fields = ['p.*'];
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $fields[] = 'd.name as name';
            $fields[] = 'd.value as value';
        }
        $sql = "select " . implode(', ', $fields) . " from " . $this->table . " p";
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $sql .= " right join " . $this->desc_table . " d on (d.id = p.id and d.`lang` = '" . $this->db->escape($this->config->get('config_language')) . "')";
        }
        $sql .= " where p.product_id = '" . (int)$product_id . "'";
        $sql .= " group by p.id";
        $sql .= " order by p.sort_order asc";

        $data = [];
        $query = $this->db->query($sql);
        foreach ($query->rows as $row) {
            $data[] = $this->getTransformer($row);
        }
        return $data;
    }
}
