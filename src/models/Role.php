<?php

namespace kevinberg\LaravelRolePerms\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $guarded = [];
}