<?php

namespace kevinberg\LaravelRolePerms\Traits;
use Illuminate\Support\Facades\Cache;
use kevinberg\LaravelRolePerms\Models\Role;
use kevinberg\LaravelRolePerms\Models\Permission;
use kevinberg\LaravelRolePerms\Facades\RolePerms;


trait Roles
{
    /**
     * Checks if the user has a specific role.
     *
     * @param String $roleName
     * @return boolean
     */
    public function hasRole(String $roleName, $entity = false): bool
    {
        # Todo use the cache!
        # $roleCache = Cache::get(config('role_perms.roles_cache_key'));

        $role = Role::where('name', $roleName)->first();

        if($role) {

            $userHasRole = RolePerms::userHasRole($this, $roleName, $entity);
            # Todo fill the cache here

            return $userHasRole;
        }

        return false;
    }

    /**
     * Checks if the user has a specific permission.
     *
     * @param String $permissionName
     * @return boolean
     */
    public function hasPermission(String $permissionName, $entity = false): bool
    {
        # Todo use the cache!
        # $permCache = Cache::get(config('role_perms.perms_cache_key'));

        $permission = Permission::where('name', $permissionName)->first();

        if($permission) {

            $userHasPerm = RolePerms::userHasPermission($this, $permissionName, $entity);
            # Todo fill the cache here

            return $userHasPerm;
        }

        return false;
    }

    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany('kevinberg\LaravelRolePerms\Models\Role', 'role_assign')
            ->withPivot('id', 'entity_type', 'entity_id')
            ->withTimestamps();
    }

    /**
     * The users that belong to the role.
     */
    public function roleResponsibilities()
    {
        return $this->belongsToMany('kevinberg\LaravelRolePerms\Models\Role', 'role_responsibilities')
            ->withPivot('id')
            ->withTimestamps();
    }
}
