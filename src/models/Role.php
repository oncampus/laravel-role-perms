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
        return $this->belongsToMany('kevinberg\LaravelRolePerms\Models\Permission', 'role_permissions');
    }

    /**
     * The users that belong to the role.
     */
    public function users()
    {
        return $this->belongsToMany('App\User', 'role_assign')->withPivot('id', 'entity_type', 'entity_id');
    }

    /**
     * The users that belong to the role.
     */
    public function responsibleUsers()
    {
        return $this->belongsToMany('App\User', 'role_responsibilities');
    }

}