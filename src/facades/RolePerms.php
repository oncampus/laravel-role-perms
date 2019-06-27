<?php

namespace kevinberg\LaravelRolePerms\Facades;
use Illuminate\Support\Facades\Facade;

class RolePerms extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'RolePerms'; }
}