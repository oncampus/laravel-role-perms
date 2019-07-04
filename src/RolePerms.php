<?php

namespace kevinberg\LaravelRolePerms;
use \App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use kevinberg\LaravelRolePerms\Models\Role;
use kevinberg\LaravelRolePerms\Models\Permission;

class RolePerms
{
    /**
     * Checks if a specific user has a specific role.
     * The results won't be cached!
     *
     * @param User $user
     * @param String $roleName
     * @return boolean
     */
    public function userHasRole(User $user, String $roleName): bool
    {
        if($user->roles && ! $user->roles->isEmpty()) {
            return $user->roles->contains('name', $roleName);
        }
        return false;
    }

    /**
     * Checks if a specific user has a specific permission.
     * The results won't be cached!
     *
     * @param User $user
     * @param String $permissionName
     * @return boolean
     */
    public function userHasPermission(User $user, String $permissionName): bool
    {
        if($user->roles && ! $user->roles->isEmpty()) {
            foreach($user->roles as $role) {
                if($role->permissions->contains('name', $permissionName)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Checks if a role has a permission.
     *
     * @param String $roleName
     * @param String $permissionName
     * @return boolean
     */
    public function roleHasPermission(String $roleName, String $permissionName): bool
    {
        $role = Role::where('name', $roleName)->first();

        if($role) {
            $permission = $role->permissions->where('name', $permissionName)->first();
            return ($permission !== null);
        }

        return false;
    }

    /**
     * Grants a role to a specific user.
     *
     * @param User $user
     * @param String $roleName
     * @return boolean
     */
    public function grantRole(User $user, String $roleName): bool
    {
        if($this->userHasRole($user, $roleName)) {
            return true;
        }

        $role = Role::where('name', $roleName)->first();

        if($role) {
            $user->roles()->syncWithoutDetaching([$role->id]);
            $user->load('roles');
            $this->clearRoleCache();
            return $this->userHasRole($user, $roleName);
        }

        return false;
    }

    /**
     * Checks if a role has a permission.
     *
     * @param String $roleName
     * @param String $permissionName
     * @return boolean
     */
    public function grantPermission(String $roleName, String $permissionName): bool
    {
        if($this->roleHasPermission($roleName, $permissionName)) {
            return true;
        }

        $role = Role::where('name', $roleName)->first();
        $permission = Permission::where('name', $permissionName)->first();

        if($role !== null && $permission !== null) {
            $role->permissions()->syncWithoutDetaching([$permission->id]);
            $role->load('permissions');
            $this->clearPermissionCache();
            return $this->roleHasPermission($roleName, $permissionName);
        }

        return false;
    }

    /**
     * Revokes a role from the user.
     *
     * @param User $user
     * @param String $roleName
     * @return boolean
     */
    public function revokeRole(User $user, String $roleName): bool
    {
        $role = Role::where('name', $roleName)->first();
        if($role !== null) {
            if($this->userHasRole($user, $roleName)) {
                $user->roles()->detach($role->id);
                $user->load('roles');
                $this->clearRoleCache();
                return !($this->userHasRole($user, $roleName));
            }

        }

        return false;
    }

    /**
     * Rectracts a permission of a role.
     * Flushes the cache!
     *
     * @param String $roleName
     * @param String $permissionName
     * @return boolean
     */
    public function revokePermission(String $roleName, String $permissionName): bool
    {
        if($this->roleHasPermission($roleName, $permissionName)) {
            $role = Role::where('name', $roleName)->first();
            $permission = Permission::where('name', $permissionName)->first();

            if($role !== null && $permission !== null) {
                $role->permissions()->detach($permission->id);
                $role->load('permissions');
                $this->clearPermissionCache();
                return !($this->roleHasPermission($roleName, $permissionName));
            }
        }

        return false;
    }

    /**
     * Clears the role cache of a user.
     *
     * @return bool
     */
    public function clearRoleCache(): bool
    {
        return Cache::forget(config('role_perms.roles_cache_key'));
    }

    /**
     * Clears the permission cache of a user.
     *
     * @return bool
     */
    public function clearPermissionCache(): bool
    {
        return Cache::forget(config('role_perms.perms_cache_key'));
    }

    /**
     * Creates a role.
     *
     * @param String $roleName
     * @return Object || boolean
     */
    public function createRole(String $roleName)
    {
        $role = new Role();
        $role->name = $roleName;

        if($role->save()) {
            return $role;
        }

        return false;
    }

    /**
     * Creates a permission.
     *
     * @param String $permissionName
     * @return Object || boolean
     */
    public function createPermission(String $permissionName)
    {
        $permission = new Permission();
        $permission->name = $permissionName;
        $permission->save();

        if($permission->save()) {
            return $permission;
        }

        return false;
    }

    /**
     * Deletes a role.
     *
     * @param String $roleName
     * @return boolean
     */
    public function deleteRole(String $roleName): bool
    {
        $role = Role::where('name', $roleName)->first();

        if($role) {
            return $role->delete();
        }

        return false;
    }

    /**
     * Deletes a permission.
     *
     * @param String $permissionName
     * @return boolean
     */
    public function deletePermission(String $permissionName): bool
    {
        $permission = Permission::where('name', $permissionName)->first();

        if($permission) {
            return $permission->delete();
        }

        return false;
    }

}
