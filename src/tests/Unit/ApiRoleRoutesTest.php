<?php

namespace bedoke\LaravelRolePerms\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use bedoke\LaravelRolePerms\Facades\RolePerms;
use bedoke\LaravelRolePerms\Models\Role;
use bedoke\LaravelRolePerms\Models\Permission;
use Illuminate\Support\Facades\Cache;
use App\User;

class ApiRoleRoutesTest extends TestCase
{
    private $roleName = 'testRole';

    private $userData = [
        'name' => 'testUser',
        'email' => 'test.user@has.no.mail',
        'password' => 'supersafepassword'
    ];

    /**
     * Tests the index route for roles.
     *
     * @return void
     */
    public function testShowRoles()
    {
        Cache::flush();
        $user = User::firstOrCreate($this->userData);

        $testRole = Role::firstOrCreate(['name' => $this->roleName]);
        Permission::firstOrCreate(['name' => 'roles.show']);
        RolePerms::grantPermission($this->roleName, 'roles.show');
        RolePerms::grantRole($user, $this->roleName);

        $response = $this->actingAs($user, 'api')->call('GET', '/api/roles/');
        $this->assertEquals(200, $response->status());
        $testRole->permissions()->sync([]);
        RolePerms::revokeRole($user, $this->roleName);
        RolePerms::deleteRole($this->roleName);
        Cache::flush();
    }

    /**
     * Tests the store route for roles.
     *
     * @return void
     */
    public function testCreateRoles()
    {
        $user = User::firstOrCreate($this->userData);

        $testRole = Role::firstOrCreate(['name' => $this->roleName]);
        Permission::firstOrCreate(['name' => 'roles.create']);
        RolePerms::grantPermission($this->roleName, 'roles.create');
        RolePerms::grantRole($user, $this->roleName);

        $role = Role::where('name', 'just_a_test_role!')->first();
        if($role !== null) {
            $role->delete();
        }

        $response = $this->actingAs($user, 'api')
                    ->call('POST', '/api/roles/', ['name' => 'just_a_test_role!']);

        $this->assertEquals(201, $response->status());
        $testRole->permissions()->sync([]);
        RolePerms::revokeRole($user, $this->roleName);
        RolePerms::deleteRole($this->roleName);
    }

    /**
     * Tests the destroy route for roles.
     *
     * @return void
     */
    public function testDeleteRoles()
    {
        $user = User::firstOrCreate($this->userData);
        $testRole = Role::firstOrCreate(['name' => $this->roleName]);
        Permission::firstOrCreate(['name' => 'roles.delete']);
        RolePerms::grantPermission($this->roleName, 'roles.delete');
        RolePerms::grantRole($user, $this->roleName);

        $role = Role::firstOrCreate(['name' => 'just_a_test_role!']);

        $response = $this->actingAs($user, 'api')
                    ->call('DELETE', '/api/roles/'.$role->id);

        $this->assertEquals(204, $response->status());

        $role = Role::where(['name' => 'just_a_test_role!'])->first();
        $this->assertNull($role);
        $testRole->permissions()->sync([]);
        RolePerms::revokeRole($user, $this->roleName);
        RolePerms::deleteRole($this->roleName);
    }

}
