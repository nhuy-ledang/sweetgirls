<?php
class User extends Model {
    protected $table = 'users';
    protected $activation_table = 'activations';
    protected $persistence_table = 'persistences';
    protected $role_table = 'roles';
    protected $role_user_table = 'role_users';
    // protected $user_ranks_table = 'user__ranks';
    protected $id;
    protected $auth_token;
    protected $permissions = [];
    protected $roles = null;
    protected $data = [];
    protected $expire = 604800; // 1 week
    protected $session_auth_token = 'auth_token';
    protected $session_user_id = 'user_id';
    protected $auth_token_name = 'Authorization';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'avatar', 'cover',
        'password', 'password_failed', 'passwords', 'permissions',
        'completed', 'completed_at',
        'latitude', 'longitude',
        'is_notify', 'is_sms', 'ip', 'last_provider',
        'device_platform', 'device_token',
        'updated_at', 'deleted_at',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'             => 'integer',
        'is_notify'      => 'boolean',
        'is_sms'         => 'boolean',
        'completed'      => 'boolean',
        'prefix'         => 'integer',
        'gender'         => 'integer',
        'address_id'     => 'integer',
        'email_verified' => 'boolean',
        'phone_verified' => 'boolean',
        'coins'          => 'integer',
        'points'         => 'integer',
    ];

    public function __construct($registry) {
        parent::__construct($registry);

        $this->db = $registry->get('db');
        $this->request = $registry->get('request');
        $this->session = $registry->get('session');
        $this->config  = $registry->get('config');

        $auth_token = false;
        if (isset($this->session->data[$this->session_auth_token])) {
            $auth_token = $this->session->data[$this->session_auth_token];
        }
        if (!$auth_token && isset($_COOKIE[$this->auth_token_name])) {
            $auth_token = $_COOKIE[$this->auth_token_name];
        }
        if ($auth_token) {
            $user_data = $this->loginByToken($auth_token);
            if (!$user_data) {
                $this->auth_token = $auth_token;
                $this->logout();
            }
        }
    }

    private function loginSuccess($user_data, $auth_token, $silent = true) {
        $this->id = (int)$user_data['id'];
        $this->data = $user_data;
        $this->getDisplay();
        $this->getAvatarUrl();
        // $this->getLevel();
        if ($auth_token) {
            $this->auth_token = $auth_token;
        } else {
            // $this->setToken();
        }
        // setcookie($this->auth_token_name, '', time() - 3600, '/');
        if ($this->auth_token) {
            setcookie($this->auth_token_name, $this->auth_token, time() + $this->expire, '/');
            $this->session->data[$this->session_user_id] = $this->id;
            $this->session->data[$this->session_auth_token] = $this->auth_token;
            if (!$silent) {
                $this->db->query("update $this->table set `ip` = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' where `id` = '" . (int)$this->id . "'");
            }

            return $user_data;
        }

        return false;
    }

    private function createToken($user_id) {
        $code = str_random(32);
        $date = date('Y-m-d H:i:s');
        // Remove all token
        // $this->db->query("delete from `" . $this->persistence_table . "` where `user_id` = '$user_id'");
        // Remove token expire
        $expires = date('Y-m-d H:i:s', time() - $this->expire);
        $this->db->query("delete from `" . $this->persistence_table . "` where (`user_id` = '$user_id' and `updated_at` < '$expires') or `code` = '$code'");
        // Create new
        $this->db->query("insert into `" . $this->persistence_table . "` (`user_id`, `code`, `updated_at`, `created_at`) values ('$user_id', '$code', '$date', '$date')");

        return $code;
    }

    private function setToken() {
        $query = $this->db->query("select * from `" . $this->persistence_table . "` where `user_id` = '$this->id' and `user_id` is not null order by `created_at` desc limit 1");

        if ($query->num_rows) {
            $this->auth_token = $query->row['code'];
        } else {
            $this->auth_token = '';
        }
    }

    public function validateCredentials(array $user, array $credentials) {
        return \Hash::check($credentials['password'], $user['password']);
    }

    public function loginByToken($auth_token, $silent = true) {
        $expires = date('Y-m-d H:i:s', time() - $this->expire);
        $user_query = $this->db->query("select * from $this->table as users where exists (select * from `" . $this->persistence_table . "` where users.id = `user_id` and `updated_at` >= '$expires' and `code` = '$auth_token') and `users`.`deleted_at` is null limit 1");
        if ($user_query->num_rows) {
            // Renewal
            $this->db->query("update `" . $this->persistence_table . "` set `updated_at` = '" . date('Y-m-d H:i:s') . "' where `user_id` = '" . $user_query->row['id'] . "' and `code` = '$auth_token'");
            return $this->loginSuccess($user_query->row, $auth_token, $silent);
        } else {
            return false;
        }
    }

    public function login($email, $password) {
        $user_query = $this->db->query("select * from $this->table where (email = '" . $this->db->escape($email) . "' or username = '" . $this->db->escape($email) . "' or phone_number = '" . $this->db->escape($email) . "') and `deleted_at` is null");

        if (!$user_query->num_rows) {
            return false;
        }

        // Check activation
        $activation_query = $this->db->query("select * from `" . $this->activation_table . "` where `user_id` = '" . (int)$user_query->row['id'] . "' and `completed` = 1 limit 1");

        if (!$activation_query->num_rows) {
            return false;
        }

        // Check password
        if (!$this->validateCredentials($user_query->row, ['password' => $password])) {
            return false;
        }

        $auth_token = $this->createToken($user_query->row['id']);

        return $this->loginSuccess($user_query->row, $auth_token, false);
    }

    public function logout() {
        unset($this->session->data[$this->session_user_id]);
        unset($this->session->data[$this->session_auth_token]);
        setcookie($this->auth_token_name, '', time() - 3600, '/');
        //setcookie($this->auth_token_name, '', time() - 3600, '/', '.tedfast.vn'); // Delete duplicate Author cookies
        if ($this->auth_token) {
            //$this->db->query("delete from `" . $this->persistence_table . "` where `user_id` = '$this->id'");
            $this->db->query("delete from `" . $this->persistence_table . "` where `code` = '$this->auth_token'");
        }
        $this->id = null;
        $this->auth_token = null;
        $this->data = [];
        setcookie($this->config->get('session_name'), '', time() - 3600, '/');
        $this->session->renew();
    }

    public function hasPermission($key, $value) {
        $has = false;
        foreach ($this->permissions as $permission) {
            if (isset($permission[$key]) && $has === false) {
                $has = in_array($value, $permission[$key]);
            }
            if ($has) {
                break;
            }
        }
        return $has;
    }

    public function isLogged() {
        return !!$this->id;
    }

    public function getId() {
        return $this->id;
    }

    public function isActivated() {
        return $this->isLogged() && $this->data['status'] == USER_STATUS_ACTIVATED;
    }

    public function getUserName() {
        if ($this->isLogged()) {
            return $this->data['username'];
        } else {
            return 'N/A';
        }
    }

    public function getEmail() {
        return $this->data['email'];
    }

    public function getNumberPhone() {
        return $this->data['phone_number'];
    }

    public function getLastProvider() {
        return $this->data['last_provider'];
    }

    public function getDisplay() {
        if ($this->isLogged()) {
            $display = trim($this->data['first_name'] . ' ' . $this->data['last_name']);
            if (!$display && $this->data['username']) {
                $display = $this->data['username'];
            }
            /*if (!$display && $this->data['phone_number']) {
                $display = $this->data['phone_number'];
            }*/
            if (!$display) {
                $display = 'N/A';
            }
            $this->data['display'] = $display;

            return $this->data['display'];
        } else {
            return 'N/A';
        }
    }

    public function getAvatarUrl() {
        if ($this->isLogged()) {
            if (!empty($this->data['avatar'])) {
                //$this->data['avatar_url'] = media_url_file($this->data['avatar'], 'small');
                $this->data['avatar_url'] = media_url_file($this->data['avatar']);
            } else if (empty($this->data['avatar_url'])) {
                // Random avatar by first name
                $display = $this->getDisplay();
                $this->data['avatar_url'] = media_url_file('/avatars/200/' . strtoupper(substr(utf8_to_ascii($display), 0, 1)) . '.jpg');
            }
            /*$last_provider = $this->getLastProvider();
            if ($last_provider == 'google' || $last_provider == 'facebook') {
                $query = $this->db->query("select avatar from `user__socials` where `provider` = '$last_provider' and `user_id` = '$this->id' limit 1");
                if ($query->num_rows) {
                    $this->data['avatar_url'] = $query->row['avatar'];
                }
            }*/
        } else {
            $this->data['avatar_url'] = '/assets/images/user-default-female.png';
        }

        return $this->data['avatar_url'];
    }

    public function getData() {
        $data = null;
        if ($this->data && is_array($this->data)) $data = $this->transform($this->data);

        return $data;
    }

    public function getProperty($prop) {
        if ($this->data && is_array($this->data) && isset($this->data[$prop])) return $this->data[$prop];

        return false;
    }

    public function getAddressId() {
        $address_id = $this->getProperty('address_id');
        return $address_id ? (int)$address_id : null;
    }

    public function getToken() {
        return $this->auth_token;
    }

    /*public function getDistance($lat, $long) {
        if (!empty($this->data) && $this->data['latitude'] && $this->data['longitude']) {
            $lat2 = $this->data['latitude'];
            $long2 = $this->data['longitude'];

            return calc_distance((double)$lat, (double)$long, (double)$lat2, (double)$long2);
        } else {
            return null;
        }
    }

    public function getGroupId() {
        return $this->data['user_group_id'];
    }*/
}
