<?php

namespace kevinberg\LaravelRolePerms\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $guarded = [];

    /**
     * The permissions that belong to the role.
     */
    public function permissions()
    {
        return $this->belongsToMany('kevinberg\LaravelRolePerms\Permission', 'role_permissions')->withTimestamps();
    }
}