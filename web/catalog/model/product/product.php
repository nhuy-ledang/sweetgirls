<?php
class ModelProductProduct extends Model {
    protected $table = DB_PREFIX . 'pd__products';
    protected $desc_table = DB_PREFIX . 'pd__product_desc';
    protected $image_table = DB_PREFIX . 'pd__product_images';
    protected $category_table = DB_PREFIX . 'pd__categories';
    protected $options_table = DB_PREFIX . 'pd__product_options';
    protected $related_table = DB_PREFIX . 'pd__product_related';
    protected $special_table = DB_PREFIX . 'pd__product_specials';
    protected $review_table = DB_PREFIX . 'pd__product_reviews';
    protected $user_viewed_table = DB_PREFIX . 'user__product_viewed';
    protected $bestseller_table = DB_PREFIX . 'pd__product_bestsellers';
    protected $latest_table = DB_PREFIX . 'pd__product_latest';
    protected $like_table = DB_PREFIX . 'pd__product_likes';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['translates', 'image', 'status', 'deleted_at'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'           => 'integer',
        'master_id'    => 'integer',
        'category_id'  => 'integer',
        'num_of_child' => 'integer',
        'is_gift'      => 'boolean',
        'is_free'      => 'boolean',
        'is_included'  => 'boolean',
        'price'        => 'double',
        'coins'        => 'integer',
        'liked'        => 'integer',
        'totalLikes'   => 'integer',
        'reviews'        => 'double',
    ];

    protected function getTransformer($row) {
        //$prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';
        //$row['href'] = $this->url->link('product/product', 'product_id=' . $row['id']);
        //['href'] = $this->config->get('config_url') . $prefix . (!empty($row['alias']) ? $row['alias'] : 'product/product?product_id=' . $row['id']);
        $this->getHref($row, 'product/product', 'product_id=' . $row['id']);
        if (isset($row['long_name'])) $row['name'] = $row['long_name'];
        if (isset($row['description'])) $row['description'] = html_entity_decode($row['description'], ENT_QUOTES, 'UTF-8');
        if (isset($row['properties'])) $row['properties'] = html_entity_decode($row['properties'], ENT_QUOTES, 'UTF-8');
        if (isset($row['user_guide'])) $row['user_guide'] = html_entity_decode($row['user_guide'], ENT_QUOTES, 'UTF-8');
        if (isset($row['image'])) {
            $row['thumb_url'] = media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8'), 'thumb');
            $row['large_url'] = media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8'), 'large');
            $row['raw_url'] = media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($row['banner'])) $row['banner_url'] = media_url_file(html_entity_decode($row['banner'], ENT_QUOTES, 'UTF-8'));
        if (isset($row['price'])) {
            $r_price = (float)$row['price'];
            if ($r_price) {
                //$price = $this->currency->format($this->tax->calculate($r_price, $row['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                $price = $this->currency->format($r_price, $this->session->data['currency']);
            } else {
                $price = false;
            }
            $row['org_price'] = $r_price;
            if (isset($row['special'])) {
                $r_special = (float)$row['special'];
                if ($r_special) {
                    //$special = $this->currency->format($this->tax->calculate($row['special'], $row['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    $special = $this->currency->format($row['special'], $this->session->data['currency']);
                } else {
                    $special = false;
                }
                $reduce = false;
                $save = false;
                if ($price && $special) {
                    $reduce = round((($r_price - $r_special) / $row['price']) * 100) . '%';
                    //$save = $this->currency->format($this->tax->calculate($r_price - $r_special, $row['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
                    $save = $this->currency->format($r_price - $r_special, $this->session->data['currency']);
                }
                $row['special'] = $special;
                $row['reduce'] = $reduce;
                $row['save'] = $save;
                $row['org_special'] = $r_special;
                $row['org_save'] = $r_price - $r_special;
            }
        }
        if (!empty($row['special_start']) && !empty($row['special_end'])) $row['flash_sale'] = true;

        return $this->transform($row);
    }

    public function updateViewed($id) {
        $this->db->query("update `" . $this->table . "` set `viewed` = (`viewed` + 1) where `id` = '" . (int)$id . "'");
        if ($this->user->getId()) {
            $query = $this->db->query("select * from " . $this->user_viewed_table . " where product_id = '" . (int)$id . "' and user_id = '" . (int)$this->user->getId() . "' and DATE(viewed_at) = '" . date('Y-m-d') . "'");
        } else {
            $query = $this->db->query("select * from `" . $this->user_viewed_table . "` where `product_id` = '" . (int)$id . "' and `ip` = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' and DATE(viewed_at) = '" . date('Y-m-d') . "'");
        }
        if ($query->num_rows) {
            $this->db->query("update `" . $this->user_viewed_table . "` set `viewed` = (`viewed` + 1), `viewed_at` = '" . date('Y-m-d H:i:s') . "' where `id` = '" . (int)$query->row['id'] . "'");
        } else {
            if ($this->user->getId()) {
                $this->db->query("insert into " . $this->user_viewed_table . " set product_id = '" . (int)$id . "', user_id = '" . (int)$this->user->getId() . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', viewed = (viewed + 1), viewed_at = '" . date('Y-m-d H:i:s') . "'");
            } else {
                $this->db->query("insert into `" . $this->user_viewed_table . "` set `product_id` = '" . (int)$id . "', `ip` = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', `viewed` = (`viewed` + 1), `viewed_at` = '" . date('Y-m-d H:i:s') . "'");
            }
        }
    }

    protected function builderSelect($alias = 'p') {
        $implode = [];
        //$implode[] = "(select price from " . DB_PREFIX . "product__discount pd2 where pd2.product_id = $alias.id and pd2.user_group_id = '" . (int)$this->config->get('config_user_group_id') . "' and pd2.quantity = '1' and ((pd2.date_start is null or UNIX_TIMESTAMP(pd2.date_start) < UNIX_TIMESTAMP('" . date('Y-m-d H:i:s') . "')) and (pd2.date_end is null or UNIX_TIMESTAMP(pd2.date_end) > UNIX_TIMESTAMP('" . date('Y-m-d H:i:s') . "'))) order by pd2.priority asc, pd2.price asc limit 1) as discount";
        $implode[] = "(select price from " . $this->special_table . " ps where ps.product_id = $alias.id and ((ps.start_date is null or UNIX_TIMESTAMP(ps.start_date) < UNIX_TIMESTAMP('" . date('Y-m-d H:i:s') . "')) and (ps.end_date is null or UNIX_TIMESTAMP(ps.end_date) > UNIX_TIMESTAMP('" . date('Y-m-d H:i:s') . "'))) order by ps.priority asc, ps.price asc limit 1) as special";
        //$implode[] = "(select points from " . DB_PREFIX . "product__reward pr where pr.product_id = $alias.id and pr.user_group_id = '" . (int)$this->config->get('config_user_group_id') . "') as reward";
        //$implode[] = "(select ss.name from " . DB_PREFIX . "stock_status ss where ss.id = $alias.stock_status_id) as stock_status";
        //$implode[] = "(select wc.unit from " . DB_PREFIX . "weight_class wc where $alias.weight_class_id = wc.id) as weight_class";
        //$implode[] = "(select lc.unit from " . DB_PREFIX . "length_class lc where $alias.length_class_id = lc.id) as length_class";
        //$implode[] = "(select count(*) from " . $this->table . " pc where pc.master_id = $alias.id) as child_total";
        $implode[] = "(select ROUND(avg(rating), 1) from " . $this->review_table . " r1 left join " . $this->table ." p1 on (r1.product_id = p1.id) where (`p1`.`id` = $alias.id or `p1`.`master_id` = $alias.id) and `r1`.`status` = 1) as rating";
        $implode[] = "(select count(*) from " . $this->review_table . " r2 left join " . $this->table ." p2 on (r2.product_id = p2.id) where (`p2`.`id` = $alias.id or `p2`.`master_id` = $alias.id) and `r2`.`status` = 1) as reviews";

        return implode(', ', $implode);
    }

    public function getProduct($id) {
        $fields = ["p.*, c.name as category, c.alias as category_alias, ps.price as special, ps.quantity as special_quantity, ps.used as special_used, ps.start_date as special_start, ps.end_date as special_end"];
        //$fields[] = "(select price from " . $this->special_table . " ps where ps.product_id = `p`.`id` and ((ps.start_date is null or UNIX_TIMESTAMP(ps.start_date) < UNIX_TIMESTAMP('" . date('Y-m-d H:i:s') . "')) and (ps.end_date is null or UNIX_TIMESTAMP(ps.end_date) > UNIX_TIMESTAMP('" . date('Y-m-d H:i:s') . "'))) order by ps.priority asc, ps.price asc limit 1) as special";
        //$fields[] = "(select ss.name from " . DB_PREFIX . "stock_status ss where ss.id = p.stock_status_id) as stock_status";
        $fields[] = "(select ROUND(avg(rating), 1) from " . $this->review_table . " r1 left join " . $this->table ." p1 on (r1.product_id = p1.id) where (`p1`.`id` = `p`.id or `p1`.`master_id` = `p`.id or `p1`.`master_id` = `p`.`master_id`) and `r1`.`status` = 1) as rating";
        $fields[] = "(select count(*) from " . $this->review_table . " r2 left join " . $this->table ." p2 on (r2.product_id = p2.id) where (`p2`.`id` = `p`.id or `p2`.`master_id` = `p`.id or `p2`.`master_id` = `p`.`master_id`) and `r2`.`status` = 1) as reviews";
        $fields[] = "(select count(*) from `" . $this->like_table . "` lk where `lk`.`product_id` = `p`.`id` and `liked` = 1) as totalLikes";
        if ($this->user->isLogged()) {
            $fields[] = "(select count(*) from `" . $this->like_table . "` lk where `lk`.`product_id` = `p`.`id` and `user_id` = " . $this->user->getId() . " and `liked` = 1) as liked";
        } else {
            $fields[] = "0 as liked";
        }
        $sql = "select " . implode(', ', $fields);
        //$sql .= ", (select group_concat(`category_id`) from `" . $this->table . "__to_category` where `product_id` = p.id) as categoryIds";
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $sql .= ", d.*";
        }
        $sql .= " from " . $this->table . " p";
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $sql .= " right join " . $this->desc_table . " d on (d.id = p.id and d.`lang` = '" . $this->db->escape($this->config->get('config_language')) . "')";
        }
        $sql .= " left join " . $this->special_table . " ps on (ps.product_id = p.id and ((ps.start_date is null or UNIX_TIMESTAMP(ps.start_date) < UNIX_TIMESTAMP('" . date('Y-m-d H:i:s') . "')) and (ps.end_date is null or UNIX_TIMESTAMP(ps.end_date) > UNIX_TIMESTAMP('" . date('Y-m-d H:i:s') . "')) and (ps.quantity is null or ps.quantity > 0)))";
        $sql .= " left join " . $this->category_table . " c on (p.category_id = c.id) where p.id = '" . (int)$id . "'";
        $sql .= " order by ps.priority asc, ps.price asc limit 1";

        $query = $this->db->query($sql);
        if ($query->num_rows) {
            return $this->getTransformer($query->row);
        } else {
            return false;
        }
    }

    public function getProducts($data = []) {
        $special_price = "(select price from " . $this->special_table . " ps where ps.product_id = p.id and ((ps.start_date is null or UNIX_TIMESTAMP(ps.start_date) < UNIX_TIMESTAMP('" . date('Y-m-d H:i:s') . "')) and (ps.end_date is null or UNIX_TIMESTAMP(ps.end_date) > UNIX_TIMESTAMP('" . date('Y-m-d H:i:s') . "'))) order by ps.priority asc, price asc limit 1)";

        $sql = "select distinct p.*";
        $sql .= ", " . $this->builderSelect('p');
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $sql .= ", d.*";
        }
        if (isset($data['sort']) && $data['sort'] == 'p.reduce') {
            $sql .= ", (select ROUND((p.price - " . $special_price . ") / p.price * 100)) as reduce";
        }
        $sql .= " from " . $this->table . " p";
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $sql .= " right join " . $this->desc_table . " d on (d.id = p.id and d.`lang` = '" . $this->db->escape($this->config->get('config_language')) . "')";
        }
        if (isset($data['filter_min']) && $data['filter_min'] !== '' || isset($data['filter_max']) && $data['filter_max'] !== '') {
            $sql .= " left join " . $this->special_table . " ps on (ps.product_id = p.id)";
        }
        $implodes = [];
        if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
            $orWhere = [];
            if (!empty($data['filter_name'])) {
                $implode = [];
                $words = $this->getWords($data['filter_name']);
                foreach ($words as $word) {
                    $implode[] = "lower(p.name) like '%" . $this->db->escape($word) . "%' or lower(p.model) like '%" . $this->db->escape($word) . "%'";
                }
                if ($implode) $orWhere[] = "(" . implode(" and ", $implode) . ")";
                if (!empty($data['filter_description'])) {
                    $orWhere[] = "lower(p.description) like '%" . $this->db->escape(utf8_strtolower((string)$data['filter_name'])) . "%'";
                }
            }
            if (!empty($data['filter_tag'])) {
                $implode = [];
                $words = $this->getWords($data['filter_tag']);
                foreach ($words as $word) {
                    $implode[] = "lower(p.tag) like '%" . $this->db->escape($word) . "%'";
                }
                if ($implode) $orWhere[] = "(" . implode(" and ", $implode) . ")";
            }
            if ($orWhere) $implodes[] = "(" . implode(" or ", $orWhere) . ")";
        }
        if (!empty($data['filter_category']) && intval($data['filter_category'])) {
            $implodes[] = "(p.category_id = '" . (int)$data['filter_category'] . "' or p.categories = '" . (int)$data['filter_category'] . "' or p.categories like '" . (int)$data['filter_category'] . ",%' or p.categories like '%," . (int)$data['filter_category'] . ",%' or p.categories like '%," . (int)$data['filter_category'] . "')";
        }
        if (isset($data['filter_manufacturer']) && $data['filter_manufacturer']) {
            $implode = [];
            $values = explode(',', $data['filter_manufacturer']);
            foreach ($values as $value) {
                $implode[] = "FIND_IN_SET ('" . (int)$value . "', p.manufacturer_id)";
            }
            if ($implode) $implodes[] = "(" . implode(" or ", $implode) . ")";
        }
        if (isset($data['filter_stock_status']) && $data['filter_stock_status']) {
            $implode = [];
            $values = explode(',', $data['filter_stock_status']);
            foreach ($values as $value) {
                $implode[] = "FIND_IN_SET ('" . (string)$value . "', p.stock_status)";
            }
            if ($implode) $implodes[] = "(" . implode(" or ", $implode) . ")";
        }
        if (isset($data['filter_categories']) && $data['filter_categories'] !== '') {
            $implode = [];
            $values = explode(',', $data['filter_categories']);
            foreach ($values as $value) {
                $implode[] = "FIND_IN_SET ('" . (int)$value . "', p.categories)";
            }
            if ($implode) $implodes[] = "(" . implode(" or ", $implode) . ")";
        }
        $newFilter = [];
//        if (!empty($data['filter_c'])) {
//            $newFilter[] = ['prop' => 'p.category_id', 'value' => $data['filter_c']];
//        }
        if (!empty($data['filter_p'])) {
            $newFilter[] = ['prop' => 'pp.property_id', 'value' => $data['filter_p']];
        }
        foreach ($newFilter as $item) {
            $whereRaw = [];
            $words = explode(',', trim($item['value']));
            foreach ($words as $word) {
//                if (trim($word)) $whereRaw[] = "lower({$item['prop']}) like '%" . $this->db->escape(utf8_strtolower(trim($word))) . "%'";
                if (trim($word)) $whereRaw[] = "{$item['prop']} = '" . $this->db->escape(utf8_strtolower(trim($word))) . "'";
            }

            if ($whereRaw) $implodes[] = "(" . implode(" or ", $whereRaw) . ")";
        }
        // Will review
        if (isset($data['filter_master_id']) && $data['filter_master_id'] !== '') {
            $implodes[] = "p.master_id = '" . (int)$data['filter_master_id'] . "'";
        } else {
            $implodes[] = "(p.master_id is null or p.master_id = 0)";
        }
        if (isset($data['filter_top']) && $data['filter_top'] !== '') {
            $implodes[] = "p.top = '" . (int)$data['filter_top'] . "'";
        }
        if (!empty($data['filter_not_in']) && is_array($data['filter_not_in'])) {
            $implodes[] = "p.id not in (" . $this->db->escape(implode(', ', $data['filter_not_in'])) . ")";
        }
        if (!empty($data['filter_in']) && is_array($data['filter_in'])) {
            $implodes[] = "p.id in (" . $this->db->escape(implode(', ', $data['filter_in'])) . ")";
        }
        if (isset($data['filter_min']) && $data['filter_min'] !== '') {
            $implodes[] = "(CASE WHEN ps.price IS NOT NULL THEN " . $special_price . " ELSE p.price END) >= '" . (int)$data['filter_min'] * 1000 . "'";
        }
        if (isset($data['filter_max']) && $data['filter_max'] !== '') {
            $implodes[] = "(CASE WHEN ps.price IS NOT NULL THEN " . $special_price . " ELSE p.price END) <= '" . (int)$data['filter_max'] * 1000 . "'";
        }
        $implodes[] = "(`p`.`is_coin_exchange` <> 1)";
        $implodes[] = "(`p`.`is_included` = 0 and `p`.`is_free` = 0)";
        $implodes[] = "(`p`.`status` = 1 and `p`.`deleted_at` is null)";
        if (!empty($implodes)) $sql .= " where " . implode(' and ', $implodes);
        $sql .= " group by p.id";
        $sort_data = ['p.name', 'p.viewed', 'p.created_at', 'p.sort_order', 'rand()'];
        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            /*if ($data['sort'] == 'p.name') {
                $sql .= " order by LCASE(" . $data['sort'] . ")";
            } else {
                $sql .= " order by " . $data['sort'];
            }*/
            $sql .= " order by " . $data['sort'];
        } else if (isset($data['sort']) && $data['sort'] == 'p.price') {
            $sql .= " order by (CASE WHEN special IS NOT NULL THEN special ELSE p.price END)";
        } else if (isset($data['sort']) && $data['sort'] == 'p.price') {
            $sql .= " order by (CASE WHEN special IS NOT NULL THEN special ELSE p.price END)";
        } else if (isset($data['sort']) && $data['sort'] == 'p.reduce') {
            $sql .= " order by reduce";
        } else {
            $sql .= " order by p.created_at desc, p.id";
        }
        if (isset($data['order']) && (strtolower($data['order']) == 'asc')) {
            $sql .= " asc";
        } else {
            $sql .= " desc";
        }
        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) $data['start'] = 0;
            if ($data['limit'] < 1) $data['limit'] = 20;
            $sql .= " limit " . (int)$data['start'] . "," . (int)$data['limit'];
        }
        $data = [];
        $query = $this->db->query($sql);
        $categoryIds = [];
        foreach ($query->rows as $row) {
            if (isset($row['category_id']) && intval($row['category_id'])) $categoryIds[] = (int)$row['category_id'];
            $data[] = $this->getTransformer($row);
        }
        $categoryIds = array_unique($categoryIds);
        if (!empty($categoryIds)) {
            $this->load->model('product/category');
            $results = $this->model_product_category->getCategories(['filter_in' => $categoryIds]);
            $categoryObj = [];
            foreach ($results as $result) $categoryObj[$result['id']] = $result;
            if (!empty($categoryObj)) {
                $newData = [];
                foreach ($data as $row) {
                    if (isset($row['category_id']) && intval($row['category_id']) && isset($categoryObj[$row['category_id']])) {
                        $category = $categoryObj[$row['category_id']];
                        $row['category'] = ['name' => $category['name'], 'href' => $category['href']];
                    }
                    $newData[] = $row;
                }
                $data = $newData;
            }
        }

        return $data;
    }

    public function getTotalProducts($data = []) {
        $special_price = "(select price from " . $this->special_table . " ps where ps.product_id = p.id and ((ps.start_date is null or UNIX_TIMESTAMP(ps.start_date) < UNIX_TIMESTAMP('" . date('Y-m-d H:i:s') . "')) and (ps.end_date is null or UNIX_TIMESTAMP(ps.end_date) > UNIX_TIMESTAMP('" . date('Y-m-d H:i:s') . "'))) order by ps.priority asc, price asc limit 1)";

        $sql = "select count(distinct p.id) as total from " . $this->table . " p";
        if (isset($data['filter_min']) && $data['filter_min'] !== '' || isset($data['filter_max']) && $data['filter_max'] !== '') {
            $sql .= " left join " . $this->special_table . " ps on (ps.product_id = p.id)";
        }
        $implodes = [];
        if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
            $orWhere = [];
            if (!empty($data['filter_name'])) {
                $implode = [];
                $words = $this->getWords($data['filter_name']);
                foreach ($words as $word) {
                    $implode[] = "lower(p.name) like '%" . $this->db->escape($word) . "%' or lower(p.model) like '%" . $this->db->escape($word) . "%'";
                }
                if ($implode) $orWhere[] = "(" . implode(" and ", $implode) . ")";
                if (!empty($data['filter_description'])) {
                    $orWhere[] = "lower(p.description) like '%" . $this->db->escape(utf8_strtolower((string)$data['filter_name'])) . "%'";
                }
            }
            if (!empty($data['filter_tag'])) {
                $implode = [];
                $words = $this->getWords($data['filter_tag']);
                foreach ($words as $word) {
                    $implode[] = "lower(p.tag) like '%" . $this->db->escape($word) . "%'";
                }
                if ($implode) $orWhere[] = "(" . implode(" and ", $implode) . ")";
            }
            if ($orWhere) $implodes[] = "(" . implode(" or ", $orWhere) . ")";
        }
        if (!empty($data['filter_category']) && intval($data['filter_category'])) {
            $implodes[] = "(p.category_id = '" . (int)$data['filter_category'] . "' or p.categories = '" . (int)$data['filter_category'] . "' or p.categories like '" . (int)$data['filter_category'] . ",%' or p.categories like '%," . (int)$data['filter_category'] . ",%' or p.categories like '%," . (int)$data['filter_category'] . "')";
        }
        if (isset($data['filter_manufacturer']) && $data['filter_manufacturer']) {
            $implode = [];
            $values = explode(',', $data['filter_manufacturer']);
            foreach ($values as $value) {
                $implode[] = "FIND_IN_SET ('" . (int)$value . "', p.manufacturer_id)";
            }
            if ($implode) $implodes[] = "(" . implode(" or ", $implode) . ")";
        }
        if (isset($data['filter_stock_status']) && $data['filter_stock_status']) {
            $implode = [];
            $values = explode(',', $data['filter_stock_status']);
            foreach ($values as $value) {
                $implode[] = "FIND_IN_SET ('" . (string)$value . "', p.stock_status)";
            }
            if ($implode) $implodes[] = "(" . implode(" or ", $implode) . ")";
        }
        if (isset($data['filter_categories']) && $data['filter_categories'] !== '') {
            $implode = [];
            $values = explode(',', $data['filter_categories']);
            foreach ($values as $value) {
                $implode[] = "FIND_IN_SET ('" . (int)$value . "', p.categories)";
            }
            if ($implode) $implodes[] = "(" . implode(" or ", $implode) . ")";
        }
        $newFilter = [];
//        if (!empty($data['filter_c'])) {
//            $newFilter[] = ['prop' => 'p.category_id', 'value' => $data['filter_c']];
//        }
        if (!empty($data['filter_p'])) {
            $newFilter[] = ['prop' => 'pp.property_id', 'value' => $data['filter_p']];
        }
        foreach ($newFilter as $item) {
            $whereRaw = [];
            $words = explode(',', trim($item['value']));
            foreach ($words as $word) {
//                if (trim($word)) $whereRaw[] = "lower({$item['prop']}) like '%" . $this->db->escape(utf8_strtolower(trim($word))) . "%'";
                if (trim($word)) $whereRaw[] = "{$item['prop']} = '" . $this->db->escape(utf8_strtolower(trim($word))) . "'";
            }
            if ($whereRaw) $implodes[] = "(" . implode(" or ", $whereRaw) . ")";
        }
        // Will review
        if (isset($data['filter_master_id']) && $data['filter_master_id'] !== '') {
            $implodes[] = "p.master_id = '" . (int)$data['filter_master_id'] . "'";
        } else {
            $implodes[] = "(p.master_id is null or p.master_id = 0)";
        }
        if (isset($data['filter_top']) && $data['filter_top'] !== '') {
            $implodes[] = "p.top = '" . (int)$data['filter_top'] . "'";
        }
        if (!empty($data['filter_not_in']) && is_array($data['filter_not_in'])) {
            $implodes[] = "p.id not in (" . $this->db->escape(implode(', ', $data['filter_not_in'])) . ")";
        }
        if (isset($data['filter_min']) && $data['filter_min'] !== '') {
            $implodes[] = "(CASE WHEN ps.price IS NOT NULL THEN " . $special_price . " ELSE p.price END) >= '" . (int)$data['filter_min'] * 1000 . "'";
        }
        if (isset($data['filter_max']) && $data['filter_max'] !== '') {
            $implodes[] = "(CASE WHEN ps.price IS NOT NULL THEN " . $special_price . " ELSE p.price END) <= '" . (int)$data['filter_max'] * 1000 . "'";
        }
        $implodes[] = "(`p`.`is_coin_exchange` <> 1)";
        $implodes[] = "(`p`.`is_included` = 0 and `p`.`is_free` = 0)";
        $implodes[] = "(`p`.`status` = 1 and `p`.`deleted_at` is null)";
        if (!empty($implodes)) $sql .= " where " . implode(' and ', $implodes);
        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getProductSpecials($data = []) {
        $fields = ['ps.product_id', 'ps.price as special', 'p.*'];
        $fields[] = "(select ROUND(avg(rating), 1) from " . $this->review_table . " r1 where `r1`.`product_id` = p.id group by `r1`.`product_id`) as rating";
        $fields[] = "(select count(*) from " . $this->review_table . " r2 where `r2`.`product_id` = p.id group by `r2`.`product_id`) as reviews";
        //foreach ($this->fields as $f) $fields[] = "p.$f";
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $fields[] = 'd.name as name';
            $fields[] = 'd.long_name as long_name';
            $fields[] = 'd.short_description as short_description';
            $fields[] = 'd.alias as alias';
        }
        $sql = "select " . implode(', ', $fields) . " from " . $this->special_table . " ps";
        $sql .= " left join " . $this->table . " p on (ps.product_id = p.id)";
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $sql .= " left join " . $this->desc_table . " d on (d.id = p.id and d.`lang` = '" . $this->db->escape($this->config->get('config_language')) . "')";
        }

        $implodes = [];
        if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
            $orWhere = [];
            if (!empty($data['filter_name'])) {
                $implode = [];
                $words = $this->getWords($data['filter_name']);
                foreach ($words as $word) {
                    $implode[] = "lower(p.name) like '%" . $this->db->escape($word) . "%' or lower(p.model) like '%" . $this->db->escape($word) . "%'";
                }
                if ($implode) $orWhere[] = "(" . implode(" and ", $implode) . ")";
                if (!empty($data['filter_description'])) {
                    $orWhere[] = "lower(p.description) like '%" . $this->db->escape(utf8_strtolower((string)$data['filter_name'])) . "%'";
                }
            }
            if (!empty($data['filter_tag'])) {
                $implode = [];
                $words = $this->getWords($data['filter_tag']);
                foreach ($words as $word) {
                    $implode[] = "lower(p.tag) like '%" . $this->db->escape($word) . "%'";
                }
                if ($implode) $orWhere[] = "(" . implode(" and ", $implode) . ")";
            }
            if ($orWhere) $implodes[] = "(" . implode(" or ", $orWhere) . ")";
        }
        if (!empty($data['filter_category']) && intval($data['filter_category'])) {
            $implodes[] = "(p.category_id = '" . (int)$data['filter_category'] . "' or p.categories = '" . (int)$data['filter_category'] . "' or p.categories like '" . (int)$data['filter_category'] . ",%' or p.categories like '%," . (int)$data['filter_category'] . ",%' or p.categories like '%," . (int)$data['filter_category'] . "')";
        }
        if (isset($data['filter_manufacturer']) && $data['filter_manufacturer']) {
            $implode = [];
            $values = explode(',', $data['filter_manufacturer']);
            foreach ($values as $value) {
                $implode[] = "FIND_IN_SET ('" . (int)$value . "', p.manufacturer_id)";
            }
            if ($implode) $implodes[] = "(" . implode(" or ", $implode) . ")";
        }

        if (isset($data['filter_categories']) && $data['filter_categories'] !== '') {
            $implode = [];
            $values = explode(',', $data['filter_categories']);
            foreach ($values as $value) {
                $implode[] = "FIND_IN_SET ('" . (int)$value . "', p.categories)";
            }
            if ($implode) $implodes[] = "(" . implode(" or ", $implode) . ")";
        }
        if (isset($data['filter_master_id']) && $data['filter_master_id'] !== '') {
            $implodes[] = "p.master_id = '" . (int)$data['filter_master_id'] . "'";
        } else {
            $implodes[] = "(p.master_id is null or p.master_id = 0)";
        }

        $newFilter = [];
        if (!empty($data['filter_p'])) {
            $newFilter[] = ['prop' => 'pp.property_id', 'value' => $data['filter_p']];
        }
        foreach ($newFilter as $item) {
            $whereRaw = [];
            $words = explode(',', trim($item['value']));
            foreach ($words as $word) {
                if (trim($word)) $whereRaw[] = "{$item['prop']} = '" . $this->db->escape(utf8_strtolower(trim($word))) . "'";
            }

            if ($whereRaw) $implodes[] = "(" . implode(" or ", $whereRaw) . ")";
        }

        $implodes[] = "(`p`.`is_coin_exchange` <> 1)";
        $implodes[] = "p.status = '1' and p.deleted_at is null";
        $implodes[] = "((ps.start_date is null or ps.start_date < NOW())";
        $implodes[] = "(ps.end_date is null or ps.end_date > NOW())) ";
        if (!empty($implodes)) $sql .= " where " . implode(' and ', $implodes);
        $sql .= " group by ps.product_id";
        $sort_data = ['p.name', 'p.viewed', 'p.created_at', 'p.sort_order', 'rand()'];
        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " order by " . $data['sort'];
        } else {
            $sql .= " order by p.created_at desc, p.id";
        }
        if (isset($data['order']) && (strtolower($data['order']) == 'asc')) {
            $sql .= " asc";
        } else {
            $sql .= " desc";
        }
        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) $data['start'] = 0;
            if ($data['limit'] < 1) $data['limit'] = 20;
            $sql .= " limit " . (int)$data['start'] . "," . (int)$data['limit'];
        }
        $data = [];
        $query = $this->db->query($sql);
        $categoryIds = [];
        foreach ($query->rows as $row) {
            if (isset($row['category_id']) && intval($row['category_id'])) $categoryIds[] = (int)$row['category_id'];
            $data[] = $this->getTransformer($row);
        }
        $categoryIds = array_unique($categoryIds);
        if (!empty($categoryIds)) {
            $this->load->model('product/category');
            $results = $this->model_product_category->getCategories(['filter_in' => $categoryIds]);
            $categoryObj = [];
            foreach ($results as $result) {
                $categoryObj[$result['id']] = $result;
            }
            if (!empty($categoryObj)) {
                $newData = [];
                foreach ($data as $row) {
                    if (isset($row['category_id']) && intval($row['category_id']) && isset($categoryObj[$row['category_id']])) {
                        $category = $categoryObj[$row['category_id']];
                        $row['category'] = ['name' => $category['name'], 'href' => $category['href']];
                    }
                    $newData[] = $row;
                }
                $data = $newData;
            }
        }

        return $data;
    }

    public function getLatestProducts($limit) {
        $data = $this->cache->get('product.latest.' . $this->config->get('config_language') . '.' . (int)$limit);
        if (!$data) {
            $fields = ['p.*'];
            //foreach ($this->fields as $f) $fields[] = "p.$f";
            if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
                $fields[] = 'd.name as name';
                $fields[] = 'd.long_name as long_name';
                $fields[] = 'd.short_description as short_description';
                $fields[] = 'd.alias as alias';
            }
            $sql = "select " . implode(', ', $fields) . " from " . $this->table . " p";
            if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
                $sql .= " right join " . $this->desc_table . " d on (d.id = p.id and d.`lang` = '" . $this->db->escape($this->config->get('config_language')) . "')";
            }
            $implodes[] = "(`p`.`is_coin_exchange` <> 1)";
            $sql .= " where p.status = '1' and p.is_coin_exchange <> '1' order by p.created_at desc limit " . (int)$limit;
            $query = $this->db->query($sql);
            $data = $query->rows;
            $this->cache->set('product.latest.' . $this->config->get('config_language') . '.' . (int)$limit, $data);
        }

        return $data;
    }

    public function getPopularProducts($limit) {
        $data = $this->cache->get('product.popular.' . $this->config->get('config_language') . '.' . (int)$limit);
        if (!$data) {
            $fields = ['p.*'];
            //foreach ($this->fields as $f) $fields[] = "p.$f";
            if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
                $fields[] = 'd.name as name';
                $fields[] = 'd.long_name as long_name';
                $fields[] = 'd.short_description as short_description';
                $fields[] = 'd.alias as alias';
            }
            $sql = "select " . implode(', ', $fields) . " from " . $this->table . " p";
            if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
                $sql .= " right join " . $this->desc_table . " d on (d.id = p.id and d.`lang` = '" . $this->db->escape($this->config->get('config_language')) . "')";
            }

            $sql .= " where p.status = '1' and p.is_coin_exchange <> '1' order by p.viewed desc, p.created_at desc limit " . (int)$limit;
            $query = $this->db->query($sql);
            $data = $query->rows;
            $this->cache->set('product.popular.' . $this->config->get('config_language') . '.' . (int)$limit, $data);
        }

        return $data;
    }

    public function getProductImages($id) {
        $query = $this->db->query("select * from " . $this->image_table . " where `product_id` = '" . (int)$id . "' order by `sort_order` asc");
        $data = [];
        foreach ($query->rows as $row) {
            if ($row['image']) {
                $data[] = [
                    'image_alt' => $row['image_alt'],
                    'thumb_url' => media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8'), 'thumb'),
                    'large_url' => media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8'), 'large'),
                    'raw_url'   => media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8')),
                ];
            }
        }
        return $data;
    }

    public function getOptions($id) {
        $sql = "select ov.* from `pd__option_values` ov left join " . $this->options_table . " po on (FIND_IN_SET(ov.id, po.value))";
        $sql .= " where po.product_id = '" . $id . "'";
        $sql .= " order by `ov`.`sort_order` asc";
        $query = $this->db->query($sql);
        $data = [];
        foreach ($query->rows as $row) {
            if (isset($row['image'])) {
                $row = array_merge($row, [
                    'thumb_url' => media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8'), 'thumb'),
                    'raw_url'   => media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8')),
                ]);
            }
            $data[] = $row;
        }
        return $data;
    }

    public function getProductRelated($id) {
        $data = [];
        $sql = "select * from " . $this->related_table . " pr left join " . $this->table . " p on (pr.related_id = p.id) where pr.product_id = '" . (int)$id . "' and p.status = '1' and p.is_coin_exchange <> '1'";
        $query = $this->db->query($sql);

        foreach ($query->rows as $result) {
            $data[$result['related_id']] = $this->getProduct($result['related_id']);
        }

        return $data;
    }

    public function getProductOptions($id) {
        $sql = "select p.id, p.name, p.long_name, o.id as option_id, o.name as option_name, o.type as option_type, ov.id as option_value_id, ov.name as option_value_name, ov.value as option_value, p.image, p.alias, p.price, p.model, ps.price as special, ps.start_date as special_start, ps.end_date as special_end";
        $sql .= " from `pd__product_variants` pv";
        $sql .= " left join `pd__options` o on (o.id = pv.option_id)";
        $sql .= " left join `pd__option_values` ov on (ov.id = pv.option_value_id)";
        $sql .= " left join (select p2.*, (select id from `pd__product_specials` ps where ps.product_id = `p2`.`id` and ((ps.start_date is null or UNIX_TIMESTAMP(ps.start_date) < UNIX_TIMESTAMP('" . date('Y-m-d H:i:s') . "')) and (ps.end_date is null or UNIX_TIMESTAMP(ps.end_date) > UNIX_TIMESTAMP('" . date('Y-m-d H:i:s') . "'))) order by ps.priority asc, ps.price asc limit 1) as ps_id from `pd__products` p2) p on (p.id = pv.product_id)";
        $sql .= " left join " . $this->special_table . " ps on (ps.id = ps_id)";
        $sql .= " where p.master_id = " . (int)$id . " and p.status = 1 and p.deleted_at is null and p.is_coin_exchange <> 1 order by o.sort_order desc, ov.sort_order asc";
        $query = $this->db->query($sql);
        $data = [];
        foreach ($query->rows as $result) {
            $option_id = (int)$result['option_id'];
            if (!isset($data[$option_id])) $data[$option_id] = ['option_id' => $option_id, 'name' => $result['option_name'], 'type' => $result['option_type'], 'products' => []];
            unset($result['option_id']);
            unset($result['option_name']);
            unset($result['option_type']);
            $data[$option_id]['products'][] = $this->getTransformer($result);
        }
        return array_values($data);
    }

    public function getProductRecentlyViewed($id, $limit) {
        $sql = "select p.*, c.name as category, c.alias as category_alias";
        $sql .= ", " . $this->builderSelect('p');
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $sql .= ", d.*";
        }
        $sql .= " from " . $this->user_viewed_table . " uv";
        $sql .= " left join " . $this->table . " p on (uv.product_id = p.id)";
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $sql .= " right join " . $this->desc_table . " d on (d.id = p.id and d.`lang` = '" . $this->db->escape($this->config->get('config_language')) . "')";
        }
        $sql .= " left join " . $this->category_table . " c on (p.category_id = c.id)";
        $sql .= " where ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' and user_id = '" . (int)$this->user->getId() . "' and (p.master_id is null or p.master_id = 0)";
        if ($id) {
            $sql .= " and uv.product_id = '" . (int)$id . "'";
        }
        $sql .= " and p.status = '1' and p.is_coin_exchange <> '1' and UNIX_TIMESTAMP(p.created_at) <= UNIX_TIMESTAMP('" . date('Y-m-d H:i:s') . "')";
        $sql .= " group by uv.product_id order by uv.viewed_at desc limit " . (int)$limit;
        $query = $this->db->query($sql);
        $data = [];
        foreach ($query->rows as $row) {
            $data[] = $this->getTransformer($row);
        }

        return $data;
    }

    public function getProductBestsellers($limit = 10) {
        $sql = "select p.*, c.name as category, c.alias as category_alias";
        $sql .= ", " . $this->builderSelect('p');
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $sql .= ", d.*";
        }
        $sql .= " from " . $this->bestseller_table . " b";
        $sql .= " left join " . $this->table . " p on (b.product_id = p.id)";
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $sql .= " right join " . $this->desc_table . " d on (d.id = p.id and d.`lang` = '" . $this->db->escape($this->config->get('config_language')) . "')";
        }
        $sql .= " left join " . $this->category_table . " c on (p.category_id = c.id)";
        $sql .= " where p.status = '1' and p.deleted_at is null and p.is_coin_exchange <> '1'";
        if ($limit) {
            $sql .= " order by b.created_at desc limit " . (int)$limit;
        }
        $query = $this->db->query($sql);
        $data = [];
        foreach ($query->rows as $row) {
            $data[] = $this->getTransformer($row);
        }

        return $data;
    }

    public function getProductLatest($limit = 10) {
        $sql = "select p.*, c.name as category, c.alias as category_alias";
        $sql .= ", " . $this->builderSelect('p');
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $sql .= ", d.*";
        }
        $sql .= " from " . $this->latest_table . " l";
        $sql .= " left join " . $this->table . " p on (l.product_id = p.id)";
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $sql .= " right join " . $this->desc_table . " d on (d.id = p.id and d.`lang` = '" . $this->db->escape($this->config->get('config_language')) . "')";
        }
        $sql .= " left join " . $this->category_table . " c on (p.category_id = c.id)";
        $sql .= " where p.status = '1' and p.deleted_at is null and p.is_coin_exchange <> '1'";
        if ($limit) {
            $sql .= " order by l.created_at desc limit " . (int)$limit;
        }
        $query = $this->db->query($sql);
        $data = [];
        foreach ($query->rows as $row) {
            $data[] = $this->getTransformer($row);
        }

        return $data;
    }

    public function getRedeemProducts() {
        $data = [];
        $sql = "select p.*, c.name as category, c.alias as catagory_alias from `" . $this->table . "` p left join `" . $this->category_table . "` c on (`category_id` = `c`.`id`) where `is_gift` = 1 and `coins` > 0 and `p`.`status` = 1 and `p`.`deleted_at` is null";
        $query = $this->db->query($sql);
        foreach ($query->rows as $result) {
            $data[] = $this->getTransformer($result);
        }
        return $data;
    }

    public function getProductLikes() {
        $sql = "select p.*, c.name as category, c.alias as category_alias";
        $sql .= ", " . $this->builderSelect('p');
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $sql .= ", d.*";
        }
        $sql .= " from " . $this->like_table . " l";
        $sql .= " left join " . $this->table . " p on (l.product_id = p.id)";
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $sql .= " right join " . $this->desc_table . " d on (d.id = p.id and d.`lang` = '" . $this->db->escape($this->config->get('config_language')) . "')";
        }
        $sql .= " left join " . $this->category_table . " c on (p.category_id = c.id)";
        $sql .= " where p.status = '1' and p.deleted_at is null and liked > 0";
        if ($this->user->isLogged()) $sql .= " and l.user_id = " . $this->user->getId();
        $query = $this->db->query($sql);
        $data = [];
        foreach ($query->rows as $row) {
            $data[] = $this->getTransformer($row);
        }

        return $data;
    }

    public function getFlashSaleProducts($limit = 10) {
        $sql = "select distinct p.*, c.name as category, c.alias as category_alias, ps.end_date as end_date, ps.quantity as special_quantity, ps.used as special_used";
        $sql .= ", " . $this->builderSelect('p');
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $sql .= ", d.*";
        }
        $sql .= " from " . $this->special_table . " ps";
        $sql .= " left join " . $this->table . " p on (ps.product_id = p.id)";
        if ($this->config->get('config_language') != $this->config->get('language_code_default')) {
            $sql .= " right join " . $this->desc_table . " d on (d.id = p.id and d.`lang` = '" . $this->db->escape($this->config->get('config_language')) . "')";
        }
        $sql .= " left join " . $this->category_table . " c on (p.category_id = c.id)";
        $sql .= " where (UNIX_TIMESTAMP(ps.start_date) < UNIX_TIMESTAMP('" . date('Y-m-d H:i:s') . "')) and (UNIX_TIMESTAMP(ps.end_date) > UNIX_TIMESTAMP('" . date('Y-m-d H:i:s') . "')) and ps.is_flashsale = '1' and p.status = '1' and p.deleted_at is null and p.is_coin_exchange <> '1' order by ps.end_date asc";
        if ($limit) {
            $sql .= " limit " . (int)$limit;
        }
        $query = $this->db->query($sql);
        $data = [];
        foreach ($query->rows as $row) {
            $data[] = $this->getTransformer($row);
        }

        return $data;
    }

    public function getIdsProductChild($master_id) {
        $data = [];
        $sql = "select id from " . $this->table . " p where p.master_id = '" . (int)$master_id . "' and p.status = '1'";
        $query = $this->db->query($sql);
        foreach ($query->rows as $result) {
            $data[] = $result['id'];
        }
        return $data;
    }
    
}
