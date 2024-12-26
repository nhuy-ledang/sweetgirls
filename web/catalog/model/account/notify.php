<?php
class ModelAccountNotify extends Model {
    protected $table = 'user__notifies';
    /**
     * The attributes that should be casted to native types.
     * @var array
     */
    protected $casts = [
        'id'      => 'integer',
        'user_id' => 'integer',
    ];

    protected function getTransformer($row) {
        if (!empty($row['created_at'])) {
            $row['created_at'] = date($this->language->get('date_format_short'), strtotime($row['created_at']));
        }

        return $this->transform($row);
    }

    public function getNotify($id) {
        $query = $this->db->query("select * from " . $this->table . " where id = '" . (int)$id . "' and user_id = '" . (int)$this->user->getId() . "'");
        if ($query->num_rows) {
            return $this->getTransformer($query->row);
        } else {
            return false;
        }
    }

    public function getNotifies($data = []) {
        $sql = "select * from " . $this->table . " where user_id = '" . (int)$this->user->getId() . "' order by id desc";
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

    public function getTotalNotifies($data = []) {
        $sql = "select count(*) as total from " . $this->table . " where user_id = '" . (int)$this->user->getId() . "'";
        if (isset($data['is_read'])) {
            if ($data['is_read'] === true || $data['is_read'] === 1) {
                $sql .= ' and is_read = 1';
            } else if ($data['is_read'] === false || $data['is_read'] === 0) {
                $sql .= ' and is_read = 0';
            }
        }
        $query = $this->db->query($sql);

        return (int)$query->row['total'];
    }
}
