<?php

namespace kevinberg\LaravelRolePerms\Traits;

trait Roles
{
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