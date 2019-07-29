<?php

namespace Oncampus\LaravelRolePerms\Models;

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
        return $this->belongsToMany('Oncampus\LaravelRolePerms\Models\Permission', 'role_permissions')
            ->withPivot('id')
            ->withTimestamps();
    }

    /**
     * The users that belong to the role.
     */
    public function users()
    {
        return $this->belongsToMany('App\User', 'role_assign')
            ->withPivot('id', 'entity_type', 'entity_id')
            ->withTimestamps();
    }

    /**
     * The users that belong to the role.
     */
    public function responsibleUsers()
    {
        return $this->belongsToMany('App\User', 'role_responsibilities')
            ->withPivot('id')
            ->withTimestamps();
    }

}
