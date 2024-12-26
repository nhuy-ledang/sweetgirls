<?php
require_once DIR_APPLICATION . 'model/page/module.base.php';
class ModelProductProductModule extends ModelPageModuleBase {
    protected $table = DB_PREFIX . 'pd__product_modules';
    protected $desc_table = DB_PREFIX . 'pd__product_module_desc';
    protected $fields = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'             => 'integer',
        'product_id'     => 'integer',
        'table_contents' => 'json',
        'table_images'   => 'json',
        'properties'     => 'json',
        'is_overwrite'   => 'boolean',
    ];

    public function getModule($id) {
        $fields = ['md.*'];
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $fields[] = 'd.name as d__name';
            $fields[] = 'd.title as d__title';
            $fields[] = 'd.short_description as d__short_description';
            $fields[] = 'd.description as d__description';
            $fields[] = 'd.table_contents as d__table_contents';
            $fields[] = 'd.table_images as d__table_images';
            $fields[] = 'd.menu_text as d__menu_text';
            $fields[] = 'd.btn_text as d__btn_text';
            $fields[] = 'd.btn_link as d__btn_link';
        }
        $sql = "select " . implode(', ', $fields) . " from " . $this->table . " md";
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $sql .= " left join `" . $this->desc_table . "` d on (d.id = md.id and d.`lang` = '" . $this->db->escape($this->config->get('config_language')) . "')";
        }
        $implode = [];
        $implode[] = "md.id = " . (int)$id;
        if (!empty($implode)) $sql .= " where " . implode(' and ', $implode);
        $query = $this->db->query($sql);
        if ($query->num_rows) {
            return $this->getTransformer($query->row);
        } else {
            return false;
        }
    }

    public function getModules($product_id) {
        $fields = ['md.*'];
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $fields[] = 'd.name as d__name';
            $fields[] = 'd.title as d__title';
            $fields[] = 'd.short_description as d__short_description';
            $fields[] = 'd.description as d__description';
            $fields[] = 'd.table_contents as d__table_contents';
            $fields[] = 'd.table_images as d__table_images';
            $fields[] = 'd.menu_text as d__menu_text';
            $fields[] = 'd.btn_text as d__btn_text';
            $fields[] = 'd.btn_link as d__btn_link';
        }
        $sql = "select " . implode(', ', $fields) . " from " . $this->table . " md";
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $sql .= " left join `" . $this->desc_table . "` d on (d.id = md.id and d.`lang` = '" . $this->db->escape($this->config->get('config_language')) . "')";
        }
        $implode = [];
        $implode[] = "md.product_id = '" . (int)$product_id . "'";
        $implode[] = "md.status = 1";
        if (!empty($implode)) $sql .= " where " . implode(' and ', $implode);
        $sql .= " group by md.id order by md.sort_order asc, LCASE(md.id) asc";
        $query = $this->db->query($sql);
        $moduleObj = [];
        $moduleIds = [];
        foreach ($query->rows as $row) {
            if ($row['module_id'] && intval($row['module_id'])) $moduleIds[] = intval($row['module_id']);
        }
        $moduleIds = array_unique($moduleIds);
        if ($moduleIds) {
            $this->load->model('page/module');
            $moduleObj = $this->model_page_module->getModules($moduleIds);
        }
        $rows = [];
        foreach ($query->rows as $row) {
            $overrideData = [];
            if ($row['module_id'] && intval($row['module_id']) && !(bool)$row['is_overwrite'] && isset($moduleObj[intval($row['module_id'])])) $overrideData = $moduleObj[intval($row['module_id'])];
            // Override properties
            $properties = json_decode($row['properties'], true);
            $override_properties = isset($overrideData['properties']) ? json_decode($overrideData['properties'], true) : '';
            if (!empty($override_properties)) {
                foreach ($properties as $k => $v) {
                    if ($v != '') $override_properties[$k] = $v;
                }
                $overrideData['properties'] = json_encode($override_properties);
            }
            if (!empty($row['name'])) $overrideData['name'] = $row['name'];
            if (!empty($row['d__name'])) $overrideData['d__name'] = $row['d__name'];
            if (!empty($row['title'])) $overrideData['title'] = $row['title'];
            if (!empty($row['d__title'])) $overrideData['d__title'] = $row['d__title'];
            if (!empty($row['tile'])) $overrideData['tile'] = $row['tile'];
            if (!empty($row['layout'])) $overrideData['layout'] = $row['layout'];
            $rows[] = $this->getTransformer(array_merge($row, $overrideData));
        }

        return $rows;
    }
}