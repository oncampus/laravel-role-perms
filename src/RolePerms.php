<?php

namespace kevinberg\LaravelRolePerms;
use \App\User;
use Illuminate\Support\Facades\DB;
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
     * @param Object|null $entity
     * @return boolean
     */
    public function userHasRole(User $user, String $roleName, ?Object $entity = null): bool
    {
        if($user->roles && ! $user->roles->isEmpty()) {

            if($entity !== null && isset($entity->id)) {

                $entityType = get_class($entity);
                $entityId = $entity->id;

            } else {

                /**
                 * $entityType and $entityId with null
                 * means we search for a globally valid role
                 * and not an assignment just for a specific
                 * entity.
                 */
                $entityType = null;
                $entityId = null;
            }

            $role = $user->roles->where('name', $roleName)->first();

            if($role !== null) {

                /**
                 * If there is no global role assignment
                 * search for the assignment with the entity.
                 */
                $assignment = DB::table('role_assign')->where([
                    'role_id' => $role->id,
                    'entity_type' => $entityType,
                    'entity_id' => $entityId
                ])->first();

                return ($assignment !== null);
            }
        }

        return false;
    }

    /**
     * Checks if a specific user has a specific permission.
     * The results won't be cached!
     *
     * @param User $user
     * @param String $permissionName
     * @param Object|null $entity
     * @return boolean
     */
    public function userHasPermission(User $user, String $permissionName, ?Object $entity = null): bool
    {
        if($user->roles && ! $user->roles->isEmpty()) {
            if($entity !== null && isset($entity->id)) {
                /**
                 * If we search a permission for a specific
                 * entity than we should first search for a global
                 * role assignment whith the permission.
                 */
                $globalAssignment = $this->userHasPermission($user, $permissionName, null);
                if($globalAssignment) {
                    return true;
                }

                $entityType = get_class($entity);
                $entityId = $entity->id;

            } else {
                /**
                 * $entityType and $entityId with null
                 * means we search for a globally valid role
                 * and not an assignment just for a specific
                 * entity.
                 */
                $entityType = null;
                $entityId = null;
            }

            foreach($user->roles as $role) {
                # check if the current role has the permission
                if($role->permissions->contains('name', $permissionName)) {
                    /**
                     * If there is no global role with the permission
                     * search for the role assignment for the entity.
                     */
                    $assignment = DB::table('role_assign')->where([
                        'role_id' => $role->id,
                        'entity_type' => $entityType,
                        'entity_id' => $entityId
                    ])->first();

                    if($assignment !== null) {
                        return true;
                    }
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
     * @param Object|null $entity
     * @return boolean
     */
    public function grantRole(User $user, String $roleName, ?Object $entity = null): bool
    {
        if($this->userHasRole($user, $roleName, $entity)) {
            return true;
        }

        $role = Role::where('name', $roleName)->first();

        if($role) {
            $assignment = array();
            if($entity !== null && isset($entity->id)) {
                $assignment = [$role->id => [
                    'entity_type' => get_class($entity),
                    'entity_id' => $entity->id
                ]];
            } else {
                $assignment = [$role->id];
            }

            $user->roles()->syncWithoutDetaching($assignment);
            $user->load('roles');
            $this->clearRoleCache($user);

            return $this->userHasRole($user, $roleName, $entity);
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
     * Revokes a role of the user.
     *
     * @param User $user
     * @param String $roleName
     * @param Object|null $entity
     * @return boolean
     */
    public function revokeRole(User $user, String $roleName, ?Object $entity = null): bool
    {
        $role = Role::where('name', $roleName)->first();
        if($role !== null) {
            if($this->userHasRole($user, $roleName, $entity)) {

                if($entity !== null && isset($entity->id)) {
                    $entityType = get_class($entity);
                    $entityId = $entity->id;
                } else {
                    $entityType = null;
                    $entityId = null;
                }

                DB::table('role_assign')->where([
                    'role_id' => $role->id,
                    'entity_type' => $entityType,
                    'entity_id' => $entityId
                ])->delete();

                $user->load('roles');
                $this->clearRoleCache($user);
                return !($this->userHasRole($user, $roleName, $entity));
            }

        }

        return false;
    }

    /**
     * Rectracts a permission of a role.
     * Flushes the permission cache!
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
     * Clears the role cache of a user or globally.
     *
     * @param User|null $user
     * @return boolean
     */
    public function clearRoleCache(?User $user = null): bool
    {
        $cacheKey = config('role_perms.roles_cache_key');
        if($user !== null) {
            $cache = Cache::get($cacheKey);
            if(is_array($cache)) {
                if(isset($cache[$user->id])) {
                    unset($cache[$user->id]);
                    return Cache::forever($cacheKey, $cache);
                }
            }
        } else {
            return Cache::forget($cacheKey);
        }
        return false;
    }

    /**
     * Clears the permission cache of a user or globally.
     *
     * @param User|null $user
     * @return boolean
     */
    public function clearPermissionCache(?User $user = null): bool
    {
        $cacheKey = config('role_perms.perms_cache_key');
        if($user !== null) {
            $cache = Cache::get($cacheKey);
            if(is_array($cache)) {
                if(isset($cache[$user->id])) {
                    unset($cache[$user->id]);
                    return Cache::forever($cacheKey, $cache);
                }
            }
        } else {
            return Cache::forget($cacheKey);
        }
        return false;
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
