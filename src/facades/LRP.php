<?php

namespace kevinberg\LaravelRolePerms\Facades;
use Illuminate\Support\Facades\Facade;

class LRP extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'LRP'; }
}