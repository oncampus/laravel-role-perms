<?php

namespace kevinberg\LaravelRolePerms;
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
            $key = config('role_perms.cache_key_prefix') . 'user_'.$user->id.'_roles';
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
            $key = config('role_perms.cache_key_prefix') . 'user_'.$user->id.'_permissions';
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
    public function userHasRole(\App\User $user, String $roleName): bool
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
    public function userHasPermission(\App\User $user, String $permissionName): bool
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
     * Checks if a specific permission exists.
     *
     * @param String $permissionName
     * @return boolean
     */
    public function permissionExists(String $permissionName): bool
    {
        return (Permission::where('name', $permissionName)->first() !== null);
    }

    /**
     * Checks if a specific role exists.
     *
     * @param String $roleName
     * @return boolean
     */
    public function roleExists(String $roleName): bool
    {
        return (Role::where('name', $roleName)->first() !== null);
    }

}