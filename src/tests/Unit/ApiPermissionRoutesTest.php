<?php

namespace kevinberg\LaravelRolePerms\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use kevinberg\LaravelRolePerms\Facades\RolePerms;
use kevinberg\LaravelRolePerms\Models\Role;
use kevinberg\LaravelRolePerms\Models\Permission;
use Illuminate\Support\Facades\Cache;
use App\User;

class ApiPermissionRoutesTest extends TestCase
{
    private $roleName = 'testRole';

    private $userData = [
        'name' => 'testUser',
        'email' => 'test.user@has.no.mail',
        'password' => 'supersafepassword'
    ];

    /**
     * Tests the index route for permissions.
     *
     * @return void
     */
    public function testShowPermissions()
    {
        Cache::flush();
        $user = User::firstOrCreate($this->userData);

        Role::firstOrCreate(['name' => $this->roleName]);
        Permission::firstOrCreate(['name' => 'permissions.show']);
        RolePerms::grantPermission($this->roleName, 'permissions.show');
        RolePerms::grantRole($user, $this->roleName);

        $response = $this->actingAs($user, 'api')->call('GET', '/api/permissions/');
        $this->assertEquals(200, $response->status());
        RolePerms::revokeRole($user, $this->roleName);
        Cache::flush();
    }

    /**
     * Tests the store route for permissions.
     *
     * @return void
     */
    public function testCreatePermissions()
    {
        $user = User::firstOrCreate($this->userData);

        Role::firstOrCreate(['name' => $this->roleName]);
        Permission::firstOrCreate(['name' => 'permissions.create']);
        RolePerms::grantPermission($this->roleName, 'permissions.create');
        RolePerms::grantRole($user, $this->roleName);

        $permission = Permission::where('name', 'just_a_test_permission!')->first();
        if($permission !== null) {
            $permission->delete();
        }

        $response = $this->actingAs($user, 'api')
                    ->call('POST', '/api/permissions/', ['name' => 'just_a_test_permission!']);

        $this->assertEquals(201, $response->status());
        RolePerms::revokeRole($user, $this->roleName);
    }

    /**
     * Tests the update route for permissions.
     *
     * @return void
     */
    public function testUpdatePermission()
    {
        $user = User::firstOrCreate($this->userData);
        Role::firstOrCreate(['name' => $this->roleName]);
        Permission::firstOrCreate(['name' => 'permissions.edit']);
        RolePerms::grantPermission($this->roleName, 'permissions.edit');
        RolePerms::grantRole($user, $this->roleName);

        $permission = Permission::firstOrCreate(['name' => 'just_a_test_permission!']);

        $response = $this->actingAs($user, 'api')
                    ->call('PATCH', '/api/permissions/'.$permission->id, ['name' => 'just_a_permission...']);

        $this->assertEquals(201, $response->status());

        $permission = Permission::where(['name' => 'just_a_permission...'])->first();
        $this->assertEquals($permission->name, 'just_a_permission...');

        $permission->delete();
        RolePerms::revokeRole($user, $this->roleName);
    }

    /**
     * Tests the destroy route for permissions.
     *
     * @return void
     */
    public function testDeletePermission()
    {
        $user = User::firstOrCreate($this->userData);
        Role::firstOrCreate(['name' => $this->roleName]);
        Permission::firstOrCreate(['name' => 'permissions.delete']);
        RolePerms::grantPermission($this->roleName, 'permissions.delete');
        RolePerms::grantRole($user, $this->roleName);

        $permission = Permission::firstOrCreate(['name' => 'just_a_test_permission!']);

        $response = $this->actingAs($user, 'api')
                    ->call('DELETE', '/api/permissions/'.$permission->id);

        $this->assertEquals(204, $response->status());

        $permission = Permission::where(['name' => 'just_a_test_permission!'])->first();
        $this->assertNull($permission);
        RolePerms::revokeRole($user, $this->roleName);
    }

}
