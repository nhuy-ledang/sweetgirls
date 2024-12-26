<?php
class ModelProductReview extends Model {
    protected $table = DB_PREFIX . 'pd__product_reviews';
    protected $image_table = DB_PREFIX . 'pd__product_review_images';
    // protected $fields = ['id', 'name', 'price', 'image', 'alias'];

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
        'id'    => 'integer',
    ];

    protected function getTransformer($row) {
        $prefix = $this->config->get('config_language') != $this->config->get('language_code_default') ? $this->config->get('config_language') . '/' : '';
        if (!empty($row['user_id'])) {
            $this->load->model('account/user');
            $user_info = $this->model_account_user->getUser($row['user_id']);
            $row['display'] = $user_info['display'];
            $row['avatar_url'] = $user_info['avatar_url'];
        }

        return $this->transform($row);
    }

    public function getReview($id) {
        $fields = ['*'];
        //$fields[] = '(select AVG(rating) from ' . $this->review_table . ' r1 where r1.subject_id = c.id group by r1.subject_id) as rating';
        $sql = "select " . implode(', ', $fields) . " from " . $this->table . " r";
        $sql .= " where r.id = '" . (int)$id . "'";
        $query = $this->db->query($sql);

        if ($query->num_rows) {
            return $this->getTransformer($query->row);
        } else {
            return false;
        }
    }

    public function getReviews($data = []) {
        $fields = ['*'];
        $sql = "select " . implode(', ', $fields);
        $sql .= " from " . $this->table . " r";
        $implode = [];
        if (!empty($data['filter_name'])) {
            $implode[] = "r.name like '%" . $this->db->escape($data['filter_name']) . "%'";
        }
        if (!empty($data['filter_product_id'])) {
            $implode[] = "FIND_IN_SET(product_id, '". $data['filter_product_id'] ."')";
        }
        if ($this->user->getId()) {
            $implode[] = "(`r`.`status` = 1 or (`r`.`user_id` = '". $this->user->getId() ."' and `r`.`status` = 0))";
        } else {
            $implode[] = "`r`.`status` = 1";
        }

        if (!empty($implode)) {
            $sql .= " where " . implode(' and ', $implode);
        }
        $sort_data = ['r.id', 'r.name', 'rand()'];
        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " order by " . $data['sort'];
            if (isset($data['order']) && (strtolower($data['order']) == 'asc')) {
                $sql .= " asc";
            } else {
                $sql .= " desc";
            }
        } else {
            $sql .= " order by r.id desc";
        }
        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) $data['start'] = 0;
            if ($data['limit'] < 1) $data['limit'] = 20;
            $sql .= " limit " . (int)$data['start'] . "," . (int)$data['limit'];
        }
        $query = $this->db->query($sql);
        $rows = [];
        foreach ($query->rows as $row) {
            $rows[] = $this->getTransformer($row);
        }

        return $rows;
    }


    public function getTotalReviews($data = []) {
        $sql = "select count(*) as total from " . $this->table . " r";
        $implode = [];
        if (!empty($data['filter_name'])) {
            $implode[] = "r.name like '%" . $this->db->escape($data['filter_name']) . "%'";
        }
        if (!empty($data['filter_product_id'])) {
            $implode[] = " FIND_IN_SET(product_id, '". $data['filter_product_id'] ."')";
        }
        $implode[] = "r.status = 1";
        $implode[] = "r.deleted_at is null";
        if (!empty($implode)) {
            $sql .= " where " . implode(' and ', $implode);
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getReviewImages($id) {
        $query = $this->db->query("select * from " . $this->image_table . " where `review_id` = '" . (int)$id . "' order by `id` asc");
        $data = [];
        foreach ($query->rows as $row) {
            if ($row['image']) {
                $data[] = [
                    'thumb_url' => media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8'), 'thumb'),
                    'large_url' => media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8'), 'large'),
                    'raw_url'   => media_url_file(html_entity_decode($row['image'], ENT_QUOTES, 'UTF-8')),
                ];
            }
        }
        return $data;
    }
}
