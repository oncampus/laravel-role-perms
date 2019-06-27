<?php

namespace kevinberg\LaravelRolePerms\Database;
use Illuminate\Database\Seeder;
use kevinberg\LaravelRolePerms\Models\Role;
use kevinberg\LaravelRolePerms\Models\Permission;
use App\User;

class Seeds extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $roleAdmin = Role::where('name', 'admin')->first();
        if(!$roleAdmin) {
            $roleAdmin = new Role();
            $roleAdmin->name = 'admin';
            $roleAdmin->save();
        }

        $wildcardPerm = Permission::where('name', '*')->first();
        if(!$wildcardPerm) {
            $wildcardPerm = new Permission();
            $wildcardPerm->name = '*';
            $wildcardPerm->save();
        }

        $roleAdmin->permissions()->attach($wildcardPerm);

        $admin = User::where('name', 'admin')->first();
        if(is_object($admin) && isset($admin->id)) {
            $roleAdmin->users()->attach($admin->id);
        }
    }
}