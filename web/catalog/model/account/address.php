<?php
class ModelAccountAddress extends Model {
    protected $table = 'user__addresses';
    protected $user_table = 'users';
    protected $country_table = 'loc__countries';
    protected $province_table = 'loc__provinces';
    protected $district_table = 'loc__districts';
    protected $ward_table = 'loc__wards';
    /**
     * The attributes that should be casted to native types.
     * @var array
     */
    protected $casts = [
        'id'             => 'integer',
        'user_id'        => 'integer',
        'country_id'     => 'integer',
        'province_id'    => 'integer',
        'district_id'    => 'integer',
        'ward_id'        => 'integer',
        'vt_province_id' => 'integer',
        'vt_district_id' => 'integer',
    ];

    protected function builder($data) {
        $output[] = "`first_name` = '" . $this->db->escape($data['first_name']) . "'";
        $output[] = "`address_1` = '" . $this->db->escape($data['address_1']) . "'";
        return $output;
    }

    public function addAddress($user_id, $data) {
        $builder = $this->builder($data);
        $builder[] = "`user_id` = '" . (int)$user_id . "'";

        $this->db->query("insert into " . $this->table . " set " . implode(', ', $builder));

        $id = $this->db->getLastId();

        if (!empty($data['default']) || !empty($data['is_default'])) {
            $this->db->query("update " . $this->user_table . " set address_id = '" . (int)$id . "', `address` = '" . $this->db->escape((string)$data['address_1']) . "' where id = '" . (int)$user_id . "'");
        }

        return $id;
    }

    public function editAddress($id, $data) {
        $builder = $this->builder($data);

        $this->db->query("update " . $this->table . " set " . implode(', ', $builder) . " where id  = '" . (int)$id . "' and user_id = '" . (int)$this->user->getId() . "'");

        if (!empty($data['default']) || !empty($data['is_default'])) {
            $this->db->query("update " . $this->user_table . " set address_id = '" . (int)$id . "', `address` = '" . $this->db->escape((string)$data['address_1']) . "' where id = '" . (int)$this->user->getId() . "'");
        }
    }

    public function deleteAddress($id) {
        $this->db->query("delete from " . $this->table . " where id = '" . (int)$id . "' and user_id = '" . (int)$this->user->getId() . "'");
    }

    public function getAddress($id) {
        $fields = ["ua.*, d.vt_province_id, d.vt_id as vt_district_id, w.vt_id as vt_ward_id, d.name as district, w.name as ward"];
        //$fields[] = "(select name from `" . $this->country_table . "` where id = ua.country_id limit 1) as country";
        //$fields[] = "(select name from `" . $this->province_table . "` where id = ua.province_id limit 1) as province";
        //$fields[] = "(select name from `" . $this->district_table . "` where id = ua.district_id limit 1) as district";
        //$fields[] = "(select name from `" . $this->ward_table . "` where id = ua.ward_id limit 1) as ward";
        $sql = "select " . implode(', ', $fields) . " from " . $this->table . " as ua";
        $sql .= " left join `" . $this->district_table . "` d on (d.id = ua.district_id)";
        $sql .= " left join `" . $this->ward_table . "` w on (w.id = ua.ward_id)";
        $sql .= " where ua.id = '" . (int)$id . "' and ua.user_id = '" . (int)$this->user->getId() . "'";
        $address_query = $this->db->query($sql);
        if ($address_query->num_rows) {
            return $this->transform($address_query->row);
        } else {
            return false;
        }
    }

    public function getAddresses() {
        $address_data = [];

        $query = $this->db->query("select * from " . $this->table . " where user_id = '" . (int)$this->user->getId() . "' order by address_1 asc");

        foreach ($query->rows as $result) {
            /*$country_query = $this->db->query("select * from `" . DB_PREFIX . "loc__country` where id = '" . (int)$result['id'] . "'");
            if ($country_query->num_rows) {
                $country = $country_query->row['name'];
                $iso_code_2 = $country_query->row['iso_code_2'];
                $iso_code_3 = $country_query->row['iso_code_3'];
                $address_format = $country_query->row['address_format'];
            } else {
                $country = '';
                $iso_code_2 = '';
                $iso_code_3 = '';
                $address_format = '';
            }

            $zone_query = $this->db->query("select * from `" . DB_PREFIX . "loc__zone` where id = '" . (int)$result['zone_id'] . "'");
            if ($zone_query->num_rows) {
                $zone = $zone_query->row['name'];
                $zone_code = $zone_query->row['code'];
            } else {
                $zone = '';
                $zone_code = '';
            }*/

            $address_data[] = array_merge($this->transform($result), [
                /*'zone'           => $zone,
                'zone_code'      => $zone_code,
                'country'        => $country,
                'iso_code_2'     => $iso_code_2,
                'iso_code_3'     => $iso_code_3,
                'address_format' => $address_format,*/
            ]);
        }

        return $address_data;
    }

    public function getTotalAddresses() {
        $query = $this->db->query("select count(*) as total from " . $this->table . " where user_id = '" . (int)$this->user->getId() . "'");

        return $query->row['total'];
    }
}
