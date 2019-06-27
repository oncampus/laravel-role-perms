<?php

namespace kevinberg\LaravelRolePerms;
use Illuminate\Support\Facades\Auth;

class LRP
{
    /**
     * Checks if the current User has a role.
     *
     * @param String $name
     * @return boolean
     */
    public function hasRole(String $name): bool
    {
        if(Auth::check()) {
            $user = Auth::user();
            if($user->roles && ! $user->roles->isEmpty()) {
                return ($user->roles()->where('name', $name)->first() !== null) ? true : false;
            }
        }
        return false;
    }

    /**
     * Checks if the current User has a permission.
     *
     * @param String $name
     * @return boolean
     */
    public function hasPermission(String $name): bool
    {
        if(Auth::check()) {
            $user = Auth::user();
            if($user->roles && ! $user->roles->isEmpty()) {
                foreach($user->roles as $role) {
                    $permission = $role->permissions->where('name', $name)->first();
                    if($permission !== null) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}