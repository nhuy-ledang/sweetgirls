<?php
class Usr extends User {
    protected $table = 'usrs';
    protected $activation_table = 'usr__activations';
    protected $persistence_table = 'usr__persistences';
    protected $role_table = 'usr__roles';
    protected $role_user_table = 'usr__role_users';
    protected $session_auth_token = 'usr__auth_token';
    protected $session_user_id = 'usr__user_id';
    protected $auth_token_name = 'sAuthorization';

    private function getRoles() {
        $sql = "select `roles`.*, `role_users`.`user_id` as `pivot_user_id`, `role_users`.`role_id` as `pivot_role_id`, `role_users`.`created_at` as `pivot_created_at`, `role_users`.`updated_at` as `pivot_updated_at` from " . $this->role_table . " as roles inner join " . $this->role_user_table . " as role_users on `roles`.`id` = `role_users`.`role_id` where `role_users`.`user_id` = '$this->id'";

        $role_query = $this->db->query($sql);

        $this->roles = $role_query->rows;

        return $this->roles;
    }

    private function inRole($role) {
        if ($this->roles === null) {
            $this->getRoles();
        }

        $check = false;

        foreach ($this->roles as $instance) {
            if ($instance['slug'] == $role || $instance['id'] == $role) {
                if (!isset($this->permissions[$role])) {
                    $this->permissions[$role] = [];

                    $permissions = json_decode($instance['permissions'], true);
                    if (is_array($permissions)) {
                        foreach ($permissions as $key => $value) {
                            $this->permissions[$role][$key] = $value;
                        }
                    }
                }
                $check = true;
            }
        }

        return $check;
    }

    public function inAnyRole(array $roles): bool {
        foreach ($roles as $role) {
            if ($this->inRole($role)) {
                return true;
            }
        }

        return false;
    }

    public function getRoleIds() {
        $ids = [];
        foreach ($this->roles as $role) {
            $ids[] = $role['id'];
        }
        return $ids;
    }

    public function isSuperAdmin() {
        return $this->isLogged() && $this->inRole('super-admin');
    }

    public function isAdmin() {
        return $this->isLogged() && $this->inAnyRole(['super-admin', 'admin']);
    }

    public function isUser() {
        return $this->isLogged() && $this->inRole('user');
    }

    public function isPoster() {
        return $this->isLogged() && $this->inRole('poster');
    }

    public function isContentCreators() {
        return $this->isLogged() && $this->inRole('content-creator');
    }

    public function isSEO() {
        return $this->isLogged() && $this->inRole('seo');
    }

    public function isAccessAdmin() {
        return $this->isSuperAdmin() || $this->isAdmin() || $this->isPoster() || $this->isContentCreators() || $this->isSEO();
    }
}
