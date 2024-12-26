<?php
class ModelAccountAffiliate extends Model {
    protected $table = DB_PREFIX . 'aff__agents';
    protected $withdrawals_table = DB_PREFIX . 'aff__agent_withdrawals';

    // Check not use
    public function addAffiliate($user_id, $data) {
        $this->db->query("INSERT INTO " . $this->table . " SET `user_id` = '" . (int)$user_id . "', `company` = '" . $this->db->escape((string)$data['company']) . "', `website` = '" . $this->db->escape((string)$data['website']) . "', `tracking` = '" . $this->db->escape(token(10)) . "', `commission` = '" . (float)$this->config->get('config_affiliate_commission') . "', `tax` = '" . $this->db->escape((string)$data['tax']) . "', `payment` = '" . $this->db->escape((string)$data['payment']) . "', `cheque` = '" . $this->db->escape((string)$data['cheque']) . "', `paypal` = '" . $this->db->escape((string)$data['paypal']) . "', `bank_name` = '" . $this->db->escape((string)$data['bank_name']) . "', `bank_branch_number` = '" . $this->db->escape((string)$data['bank_branch_number']) . "', `bank_swift_code` = '" . $this->db->escape((string)$data['bank_swift_code']) . "', `bank_account_name` = '" . $this->db->escape((string)$data['bank_account_name']) . "', `bank_account_number` = '" . $this->db->escape((string)$data['bank_account_number']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']['affiliate']) ? json_encode($data['custom_field']['affiliate']) : '') . "', `status` = '" . (int)!$this->config->get('config_affiliate_approval') . "'");

        /*if ($this->config->get('config_affiliate_approval')) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "user_approval` SET user_id = '" . (int)$user_id . "', type = 'affiliate', date_added = NOW()");
        }*/
    }

    // Check not use
    public function editAffiliate($user_id, $data) {
        $this->db->query("UPDATE " . $this->table . " SET `company` = '" . $this->db->escape((string)$data['company']) . "', `website` = '" . $this->db->escape((string)$data['website']) . "', `commission` = '" . (float)$this->config->get('config_affiliate_commission') . "', `tax` = '" . $this->db->escape((string)$data['tax']) . "', `payment` = '" . $this->db->escape((string)$data['payment']) . "', `cheque` = '" . $this->db->escape((string)$data['cheque']) . "', `paypal` = '" . $this->db->escape((string)$data['paypal']) . "', `bank_name` = '" . $this->db->escape((string)$data['bank_name']) . "', `bank_branch_number` = '" . $this->db->escape((string)$data['bank_branch_number']) . "', `bank_swift_code` = '" . $this->db->escape((string)$data['bank_swift_code']) . "', `bank_account_name` = '" . $this->db->escape((string)$data['bank_account_name']) . "', `bank_account_number` = '" . $this->db->escape((string)$data['bank_account_number']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']['affiliate']) ? json_encode($data['custom_field']['affiliate']) : '') . "' WHERE `user_id` = '" . (int)$user_id . "'");
    }

    public function getAffiliate($user_id) {
        $query = $this->db->query("SELECT * FROM `" . $this->table . "` WHERE `user_id` = '" . (int)$user_id . "' and `status` > 0 and `deleted_at` is null");
        if(!empty($query->row['id_front'])) {
            $query->row['id_front_url'] = media_url_file($query->row['id_front']);
        }
        if(!empty($query->row['id_behind'])) {
            $query->row['id_behind_url'] = media_url_file($query->row['id_behind']);
        }
        return $query->num_rows ? $query->row : null;
    }

    public function getAffiliateByTracking($code) {
        $query = $this->db->query("SELECT * FROM `" . $this->table . "` WHERE `code` = '" . $this->db->escape($code) . "'");

        return $query->row;
    }

    // Check not use
    public function addAffiliateReport($user_id, $ip, $country = '') {
        $this->db->query("INSERT INTO `aff__agent_reports` SET user_id = '" . (int)$user_id . "', ip = '" . $this->db->escape($ip) . "', country = '" . $this->db->escape($country) . "', date_added = NOW()");
    }

    public function getTotalWithdrawals($agent_id, $type = '') {
        $sql = "SELECT SUM(`points`) AS total FROM `" . $this->withdrawals_table ."` WHERE `agent_id` = '" . (int)$agent_id . "'";
        if ($type) {
            $sql .= " AND `" .(string)$type . "` is not null";
        }
        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getTotalPoints($agent_id, $type = '') {
        $sql = "SELECT sum(points) AS total FROM `aff__agent_points` WHERE `agent_id` = '" . (int)$agent_id . "' and type = 'in' and status = 1 and deleted_at is null;";
        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getTypes() {
        $sql = "SELECT * FROM `aff__ticket_types` WHERE status = 1";
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTicket($id) {
        $sql = "SELECT * FROM `aff__tickets` WHERE user_id = ". $this->user->getId() ." AND id = ". (int)$id ." ORDER BY id DESC";
        $query = $this->db->query($sql);
        if ($query->row) {
            $sql_rep = "SELECT * FROM `aff__ticket_reps` WHERE ticket_id = ". $query->row['id'];
            $query_rep = $this->db->query($sql_rep);
            $data_rep = [];
            foreach ($query_rep->rows as $row) {
                $sql_file = "SELECT * FROM `aff__ticket_files` WHERE ticket_id = ". $query->row['id'] . " AND `type` = 'rep' AND object_id = " . $row['id'];
                $query_file = $this->db->query($sql_file);
                if ($query_file->rows) {
                    foreach ($query_file->rows as $file) {
                        $row['files'][] = media_url_file(html_entity_decode($file['path'], ENT_QUOTES, 'UTF-8'));
                    }
                }
                $data_rep[] = $row;
            }
            $query->row['reps'] = $data_rep;

            // Get file in ticket
            $f = "SELECT * FROM `aff__ticket_files` WHERE ticket_id = ". $id . " AND `type` = 'ticket'";
            $query_f = $this->db->query($f);
            if ($query_f->rows) {
                foreach ($query_f->rows as $file) {
                    $query->row['files'][] = media_url_file(html_entity_decode($file['path'], ENT_QUOTES, 'UTF-8'));
                }
            }
        }

        return $query->row;
    }

    public function getTickets() {
        $sql = "SELECT * FROM `aff__tickets` WHERE user_id = ". $this->user->getId() ." ORDER BY id DESC";
        $query = $this->db->query($sql);
        $data = [];
        foreach ($query->rows as $ticket) {
            $sql_rep = "SELECT * FROM `aff__ticket_reps` WHERE ticket_id = ". $ticket['id'];
            $query_rep = $this->db->query($sql_rep);
            $data_rep = [];
            foreach ($query_rep->rows as $row) {
                // Get file in reply
                $sql_file = "SELECT * FROM `aff__ticket_files` WHERE ticket_id = ". $ticket['id'] . " AND `type` = 'rep' AND object_id = " . $row['id'];
                $query_file = $this->db->query($sql_file);
                if ($query_file->rows) {
                    foreach ($query_file->rows as $file) {
                        $row['files'][] = media_url_file(html_entity_decode($file['path'], ENT_QUOTES, 'UTF-8'));
                    }
                }
                $data_rep[] = $row;
            }
            $ticket['reps'] = $data_rep;

            // Get file in ticket
            $f = "SELECT * FROM `aff__ticket_files` WHERE ticket_id = ". $ticket['id'] . " AND `type` = 'ticket'";
            $query_f = $this->db->query($f);
            if ($query_f->rows) {
                foreach ($query_f->rows as $file) {
                    $ticket['files'][] = media_url_file(html_entity_decode($file['path'], ENT_QUOTES, 'UTF-8'));
                }
            }
            $data[] = $ticket;
        }

        return $data;
    }
}
