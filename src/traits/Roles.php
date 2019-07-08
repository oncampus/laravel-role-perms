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
     * @param Object $entity
     * @param Bool $useCache
     * @return boolean
     */
    public function hasRole(String $roleName, $entity = false, bool $useCache = true): bool
    {

        $roleCache = Cache::get(config('role_perms.roles_cache_key'));

        if(!is_array($roleCache)) {
            $roleCache = [];
        }

        if(is_object($entity) && isset($entity->id)) {
            $entityType = get_class($entity);
            $entityId = $entity->id;
        } else {
            $entityType = 'null';
            $entityId = 'null';
        }

        if($useCache) {
            if(isset($roleCache[$this->id][$roleName][$entityType][$entityId])) {
                return $roleCache[$this->id][$roleName][$entityType][$entityId];
            }
        }

        $role = Role::where('name', $roleName)->first();

        if($role) {

            $userHasRole = RolePerms::userHasRole($this, $roleName, $entity);

            if($useCache) {
                # build and write cache entry
                $roleCache[$this->id][$roleName][$entityType][$entityId] = $userHasRole;
                Cache::forever(
                    config('role_perms.roles_cache_key'),
                    $roleCache
                );
            }

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
    public function hasPermission(String $permissionName, $entity = false, bool $useCache = true): bool
    {

        $permCache = Cache::get(config('role_perms.perms_cache_key'));

        if(!is_array($permCache)) {
            $permCache = [];
        }

        if(is_object($entity) && isset($entity->id)) {
            $entityType = get_class($entity);
            $entityId = $entity->id;
        } else {
            $entityType = 'null';
            $entityId = 'null';
        }

        if($useCache) {
            if(isset($permCache[$this->id][$permissionName][$entityType][$entityId])) {
                return $permCache[$this->id][$permissionName][$entityType][$entityId];
            }
        }

        $permission = Permission::where('name', $permissionName)->first();

        if($permission) {

            $userHasPerm = RolePerms::userHasPermission($this, $permissionName, $entity);

            if($useCache) {
                # build and write cache entry
                $permCache[$this->id][$permissionName][$entityType][$entityId] = $userHasPerm;
                Cache::forever(
                    config('role_perms.roles_cache_key'),
                    $permCache
                );
            }

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
