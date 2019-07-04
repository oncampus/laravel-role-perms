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
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);

        $permissions = [
            Permission::firstOrCreate(['name' => '*'])->id,
            Permission::firstOrCreate(['name' => 'permissions.show'])->id,
            Permission::firstOrCreate(['name' => 'permissions.create'])->id,
            Permission::firstOrCreate(['name' => 'permissions.edit'])->id,
            Permission::firstOrCreate(['name' => 'permissions.delete'])->id,
        ];

        $roleAdmin->permissions()->syncWithoutDetaching($permissions);

        $admin = User::where('name', 'admin')->first();
        if(is_object($admin) && isset($admin->id)) {
            $roleAdmin->users()->syncWithoutDetaching([$admin->id]);
        }
    }
}
