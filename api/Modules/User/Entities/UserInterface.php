<?php namespace Modules\User\Entities;

interface UserInterface {
    /**
     * Checks if a user belongs to the given Role ID
     * @param  int $roleId
     * @return bool
     */
    public function hasRoleId($roleId);

    /**
     * Checks if a user belongs to the given Role Name
     * @param  string $name
     * @return bool
     */
    public function hasRoleName($name);

    /**
     * Checks if a user belongs to the given Group ID
     * @param  int $groupId
     * @return bool
     */
    public function hasGroupId($groupId);

    /**
     * Checks if a user belongs to the given Group Name
     * @param  string $name
     * @return bool
     */
    public function hasGroupName($name);

    /**
     * Check if the current user is activated
     * @return bool
     */
    public function isActivated();
}
