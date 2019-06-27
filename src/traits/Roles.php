<?php

namespace kevinberg\LaravelRolePerms\Traits;

trait Roles
{
    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany('kevinberg\LaravelRolePerms\Models\Role', 'user_roles');
    }
}