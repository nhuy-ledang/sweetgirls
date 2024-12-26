<?php
class ModelAccountUser extends Model {
    protected $table = DB_PREFIX . 'users';

    /**
     * The attributes that should be hidden for serialization.
     * @var array
     */
    protected $hidden = [
        'avatar', 'cover', 'user_group_id',
        'password', 'password_failed', 'passwords', 'permissions',
        'completed', 'completed_at',
        'latitude', 'longitude',
        'is_notify', 'is_sms', 'ip', 'last_provider',
        'device_platform', 'device_token',
        'created_at', 'updated_at', 'deleted_at',
    ];

    /**
     * The attributes that should be casted to native types.
     * @var array
     */
    protected $casts = [
        'id'             => 'integer',
        'is_notify'      => 'boolean',
        'is_sms'         => 'boolean',
        'completed'      => 'boolean',
        'email_verified' => 'boolean',
        'phone_verified' => 'boolean',
        'prefix'         => 'integer',
        'gender'         => 'integer',
    ];

    protected function getTransformer($row) {
        $display = trim($row['first_name'] . ' ' . $row['last_name']);
        if (!$display && $row['username']) {
            $display = $row['username'];
        }
        if (!$display) {
            $display = 'N/A';
        }
        $row['display'] = $display;
        if (!empty($row['avatar'])) {
            //$row['avatar_url'] = media_url_file($row['avatar'], 'small');
            $row['avatar_url'] = media_url_file($row['avatar']);
        } else if (empty($row['avatar_url'])) {
            $row['avatar_url'] = media_url_file('/avatars/200/' . strtoupper(substr(utf8_to_ascii($display), 0, 1)) . '.jpg');
        }

        return $this->transform($row);
    }

    /**
     * Return a random string for an activation code.
     * @return string
     * @throws Exception
     */
    protected function generateActivationCode() {
        return str_random(32);
    }

    protected function builder(&$data) {
        $e = explode('@', $data['email']);
        $username = $e[0];
        $total = $this->getTotalUsersByUsername($username);
        if ($total) {
            $username = $username . ($total + 1);
        }

        $output[] = "`email` = '" . $this->db->escape($data['email']) . "'";
        $output[] = "`password` = '" . $this->db->escape(\Hash::make($data['password'])) . "'";
        $output[] = "`username` = '" . $this->db->escape($username) . "'";

        return $output;
    }

    public function addUser($data) {
        $now = date('Y-m-d H:i:s');

        $builder = $this->builder($data);
        $builder[] = "`status` = '" . USER_STATUS_ACTIVATED . "'";
        $builder[] = "`completed` = '1'";
        $builder[] = "`completed_at` = '$now'";
        $builder[] = "`updated_at` = '$now'";
        $builder[] = "`created_at` = '$now'";

        $this->db->query("INSERT INTO " . $this->table . " SET " . implode(', ', $builder));

        $id = $this->db->getLastId();

        // Create role
        $this->db->query("INSERT INTO `role_users` (`id`, `role_id`, `created_at`, `updated_at`) VALUES ($id, " . USER_ROLE_USER . ", '$now','$now')");

        // Activations
        $code = $this->generateActivationCode();

        $this->db->query("insert into `activations` (`code`, `id`, `completed`, `completed_at`, `updated_at`, `created_at`) values ('$code', $id, 1, '$now', '$now', '$now')");
    }

    public function getUserByEmail($email) {
        $query = $this->db->query("SELECT * FROM " . $this->table . " WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "' and `deleted_at` is null");

        return $query->row;
    }

    public function getTotalUsersByUsername($username) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . $this->table . " WHERE username = '" . $this->db->escape($username) . "'");

        return $query->row['total'];
    }

    public function getIps($id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "user__ip` WHERE id = '" . (int)$id . "'");

        return $query->rows;
    }

    public function getTotalIps($id) {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "user__ip WHERE id = '" . (int)$id . "'");

        return $query->row['total'];
    }

    public function addLogin($id, $ip, $country = '') {
        $this->db->query("INSERT INTO " . DB_PREFIX . "user__ip SET id = '" . (int)$id . "', ip = '" . $this->db->escape($ip) . "', country = '" . $this->db->escape($country) . "', created_at = NOW()");
    }

    public function addLoginAttempt($email) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user__login WHERE email = '" . $this->db->escape(utf8_strtolower((string)$email)) . "' AND ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'");

        if (!$query->num_rows) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "user__login SET email = '" . $this->db->escape(utf8_strtolower((string)$email)) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', total = 1, created_at = '" . $this->db->escape(date('Y-m-d H:i:s')) . "', updated_at = '" . $this->db->escape(date('Y-m-d H:i:s')) . "'");
        } else {
            $this->db->query("UPDATE " . DB_PREFIX . "user__login SET total = (total + 1), updated_at = '" . $this->db->escape(date('Y-m-d H:i:s')) . "' WHERE id = '" . (int)$query->row['id'] . "'");
        }
    }

    public function getLoginAttempts($email) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "user__login` WHERE email = '" . $this->db->escape(utf8_strtolower($email)) . "'");

        return $query->row;
    }

    public function deleteLoginAttempts($email) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "user__login` WHERE email = '" . $this->db->escape(utf8_strtolower($email)) . "'");
    }

    //*********** NOT USE *******************

	public function editUser($id, $data) {
//		$this->db->query("UPDATE " . $this->table . " SET first_name = '" . $this->db->escape((string)$data['first_name']) . "', last_name = '" . $this->db->escape((string)$data['last_name']) . "', email = '" . $this->db->escape((string)$data['email']) . "', phone_number = '" . $this->db->escape((string)$data['phone_number']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']['account']) ? json_encode($data['custom_field']['account']) : '') . "' WHERE id = '" . (int)$id . "'");
		$this->db->query("UPDATE " . $this->table . " SET first_name = '" . $this->db->escape((string)$data['first_name']) . "', last_name = '" . $this->db->escape((string)$data['last_name']) . "', email = '" . $this->db->escape((string)$data['email']) . "', phone_number = '" . $this->db->escape((string)$data['phone_number']) . "' WHERE id = '" . (int)$id . "'");
	}

	public function editPassword($email, $password) {
//		$this->db->query("UPDATE " . $this->table . " SET salt = '', password = '" . $this->db->escape(password_hash(html_entity_decode($password, ENT_QUOTES, 'UTF-8'), PASSWORD_DEFAULT)) . "', code = '' WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
		$this->db->query("UPDATE " . $this->table . " SET password = '" . $this->db->escape(password_hash(html_entity_decode($password, ENT_QUOTES, 'UTF-8'), PASSWORD_DEFAULT)) . "' WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
	}

	public function editAddressId($id, $address_id) {
		$this->db->query("UPDATE " . $this->table . " SET address_id = '" . (int)$address_id . "' WHERE id = '" . (int)$id . "'");
	}

	public function editCode($email, $code) {
		$this->db->query("UPDATE `" . $this->table . "` SET code = '" . $this->db->escape($code) . "' WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
	}

	public function editToken($email, $token) {
		$this->db->query("UPDATE `" . $this->table . "` SET token = '" . $this->db->escape($token) . "' WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
	}

	public function editNewsletter($newsletter) {
		$this->db->query("UPDATE " . $this->table . " SET newsletter = '" . (int)$newsletter . "' WHERE id = '" . (int)$this->user->getId() . "'");
	}

	public function getUser($id) {
		$query = $this->db->query("SELECT * FROM " . $this->table . " WHERE id = '" . (int)$id . "'");

        if ($query->num_rows) {
            return $this->getTransformer($query->row);
        } else {
            return false;
        }
	}

	public function getUserByCode($code) {
		$query = $this->db->query("SELECT id, first_name, last_name, email FROM `" . $this->table . "` WHERE code = '" . $this->db->escape($code) . "' AND code != ''");

		return $query->row;
	}

	public function getUserByToken($token) {
		$query = $this->db->query("SELECT * FROM " . $this->table . " WHERE token = '" . $this->db->escape($token) . "' AND token != ''");

		$this->db->query("UPDATE " . $this->table . " SET token = ''");

		return $query->row;
	}

	public function getTotalUsersByEmail($email) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . $this->table . " WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row['total'];
	}

	public function addTransaction($id, $description, $amount = '', $order_id = 0) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "user__transaction SET id = '" . (int)$id . "', order_id = '" . (float)$order_id . "', description = '" . $this->db->escape($description) . "', amount = '" . (float)$amount . "', created_at = NOW()");
	}

	public function deleteTransactionByOrderId($order_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "user__transaction WHERE order_id = '" . (int)$order_id . "'");
	}

	public function getTransactionTotal($id) {
		$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "user__transaction WHERE id = '" . (int)$id . "'");

		return $query->row['total'];
	}

	public function getTotalTransactionsByOrderId($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "user__transaction WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}

	public function getRewardTotal($id) {
		$query = $this->db->query("SELECT SUM(points) AS total FROM " . DB_PREFIX . "user__reward WHERE id = '" . (int)$id . "'");

		return $query->row['total'];
	}
}
