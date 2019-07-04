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
    public function hasRole(String $roleName): bool
    {
        $roleCache = Cache::get(config('role_perms.roles_cache_key'));

        if(is_null($roleCache) || !is_array($roleCache)) {
            $roleCache = array();
        }

        # try to get the result from cache.
        if(array_key_exists($this->id, $roleCache)) {
            if(is_array($roleCache[$this->id]) && array_key_exists($roleName, $roleCache[$this->id])) {
                return $roleCache[$this->id][$roleName];
            }
        }

        # calculate result and store it in the cache.
        $role = Role::where('name', $roleName)->first();
        if($role) {
            $userHasRole = RolePerms::userHasRole($this, $roleName);
            $roleCache[$this->id][$roleName] = $userHasRole;
            Cache::forever(config('role_perms.roles_cache_key'), $roleCache);
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
    public function hasPermission(String $permissionName): bool
    {
        $permCache = Cache::get(config('role_perms.perms_cache_key'));

        if(is_null($permCache) || !is_array($permCache)) {
            $permCache = array();
        }

        # try to get the result from cache.
        if(array_key_exists($this->id, $permCache)) {
            if(is_array($permCache[$this->id]) && array_key_exists($permissionName, $permCache[$this->id])) {
                return $permCache[$this->id][$permissionName];
            }
        }

        # calculate result and store it in the cache.
        $permission = Permission::where('name', $permissionName)->first();
        if($permission) {
            $userHasPerm = RolePerms::userHasPermission($this, $permissionName);
            $permCache[$this->id][$permissionName] = $userHasPerm;
            Cache::forever(config('role_perms.perms_cache_key'), $permCache);
            return $userHasPerm;
        }

        return false;
    }

    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany('kevinberg\LaravelRolePerms\Models\Role', 'role_assign')->withPivot('id', 'entity_type', 'entity_id');
    }

    /**
     * The users that belong to the role.
     */
    public function roleResponsibilities()
    {
        return $this->belongsToMany('kevinberg\LaravelRolePerms\Models\Role', 'role_responsibilities');
    }
}
