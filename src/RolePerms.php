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
     * Checks if the current User has a role.
     * The results will be cached.
     *
     * @param String $roleName
     * @return boolean
     */
    public function hasRole(String $roleName): bool
    {
        if(Auth::check()) {

            $user = Auth::user();
            $key = $this->getRoleCacheKey($user);
            $cached = Cache::get($key);

            if(is_null($cached) || ! is_array($cached)) {

                # the user has no cache entry at the moment.
                if($this->RoleExists($roleName)) {
                    $userHasRole = $this->userHasRole($user, $roleName);
                    Cache::forever($key, array($roleName => $userHasRole));
                    return $userHasRole;
                }

            } else {

                # the user already has a cache entry.
                if(array_key_exists($roleName, $cached)) {

                    # the role is already stored in the user cache.
                    return $cached[$roleName];

                } else {

                    # the result for the roleName is not stored in the user cache. Do it now.
                    if($this->RoleExists($roleName)) {
                        $userHasRole = $this->userHasRole($user, $roleName);
                        $cached[$roleName] = $userHasRole;
                        Cache::forever($key, $cached);
                        return $userHasRole;
                    }

                }
            }
            return false;
        }
        return false;
    }

    /**
     * Checks if the current User has a permission.
     * The results will be cached.
     *
     * @param String $roleName
     * @return boolean
     */
    public function hasPermission(String $permissionName): bool
    {
        if(Auth::check()) {

            $user = Auth::user();
            $key = $this->getPermissionCacheKey($user);
            $cached = Cache::get($key);

            if(is_null($cached) || ! is_array($cached)) {

                # the user has no cache entry at the moment.
                if($this->permissionExists($permissionName)) {
                    $userHasPermission = $this->userHasPermission($user, $permissionName);
                    Cache::forever($key, array($permissionName => $userHasPermission));
                    return $userHasPermission;
                }

            } else {

                # the user already has a cache entry.
                if(array_key_exists($permissionName, $cached)) {

                    # the permission is already stored in the user cache.
                    return $cached[$permissionName];

                } else {

                    if($this->permissionExists($permissionName)) {
                        # the result for the permissionName is not stored in the user cache. Do it now.
                        $userHasPermission = $this->userHasPermission($user, $permissionName);
                        $cached[$permissionName] = $userHasPermission;
                        Cache::forever($key, $cached);
                        return $userHasPermission;
                    }

                }
            }
            return false;

        }
        return false;
    }

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

            return true;
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

        if($role) {

            $user->roles()->detach($role->id);

            $key = $this->getRoleCacheKey($user);
            $cached = Cache::get($key);

            if($cached !== null) {
                if(is_array($cached)) {
                    if(array_key_exists($roleName, $cached)) {
                        unset($cached[$roleName]);
                        Cache::forever($key, $cached);
                    }
                } else {
                    # This case is very unlikely, but if it occurs something is wrong with the cache.
                    $this->clearRoleCache($user);
                }
            }

            return true;
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
                Cache::flush();
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the cache key for roles.
     *
     * @param User $user
     * @return void
     */
    public function getRoleCacheKey(User $user): String
    {
        return config('role_perms.cache_key_prefix') . '_user_'.$user->id.'_roles';
    }

    /**
     * Returns the cache key for permissions.
     *
     * @param User $user
     * @return void
     */
    public function getPermissionCacheKey(User $user): String
    {
        return config('role_perms.cache_key_prefix') . '_user_'.$user->id.'_permissions';
    }

    /**
     * Clears the role cache of a user.
     *
     * @param User $user
     * @return void
     */
    public function clearRoleCache(User $user): bool
    {
        return Cache::forget($this->getRoleCacheKey($user));
    }

    /**
     * Clears the permission cache of a user.
     *
     * @param User $user
     * @return void
     */
    public function clearPermissionCache(User $user): bool
    {
        return Cache::forget($this->getPermissionCacheKey($user));
    }

}