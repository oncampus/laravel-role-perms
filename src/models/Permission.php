<?php

namespace kevinberg\LaravelRolePerms\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';
    protected $guarded = [];

    /**
     * The permissions that belong to the role.
     */
    public function roles()
    {
        return $this->belongsToMany('kevinberg\LaravelRolePerms\Models\Role', 'role_permissions');
    }
}