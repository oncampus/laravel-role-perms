<?php

namespace kevinberg\LaravelRolePerms\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use kevinberg\LaravelRolePerms\Facades\RolePerms;
use kevinberg\LaravelRolePerms\Models\Role;
use kevinberg\LaravelRolePerms\Models\Permission;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\User;

class ApiPermissionAssignRoutesTest extends TestCase
{
    private $roleName = 'testRole';

    private $userData = [
        'name' => 'testUser',
        'email' => 'test.user@has.no.mail',
        'password' => 'supersafepassword'
    ];

    /**
     * Tests the index route for permission assigns.
     *
     * @return void
     */
    public function testShowPermissionAssigns()
    {
        Cache::flush();
        $user = User::firstOrCreate($this->userData);

        $testRole = Role::firstOrCreate(['name' => $this->roleName]);
        Permission::firstOrCreate(['name' => 'permissions.assigns.show']);
        RolePerms::grantPermission($this->roleName, 'permissions.assigns.show');
        RolePerms::grantRole($user, $this->roleName);

        $response = $this->actingAs($user, 'api')->call('GET', 'api/permission_assigns');
        $this->assertEquals(200, $response->status());
        $testRole->permissions()->sync([]);
        RolePerms::revokeRole($user, $this->roleName);
        RolePerms::deleteRole($this->roleName);
        Cache::flush();
    }

    /**
     * Tests the store route for permission assigns.
     *
     * @return void
     */
    public function testCreatePermissionAssigns()
    {
        $user = User::firstOrCreate($this->userData);

        $testRole = Role::firstOrCreate(['name' => $this->roleName]);
        Permission::firstOrCreate(['name' => 'permissions.assigns.create']);
        RolePerms::grantPermission($this->roleName, 'permissions.assigns.create');
        RolePerms::grantRole($user, $this->roleName);

        Permission::firstOrCreate(['name' => 'Great permission']);

        $response = $this->actingAs($user, 'api')
                    ->call('POST', '/api/permission_assigns/', [
                        'permission_name' => 'Great permission',
                        'role_name' => $this->roleName,
                    ]);

        $this->assertEquals(201, $response->status());
        $testRole->permissions()->sync([]);
        RolePerms::revokeRole($user, $this->roleName);
        RolePerms::deletePermission('Great permission');
        RolePerms::deleteRole($this->roleName);
    }

    /**
     * Tests the destroy route for permission assigns.
     *
     * @return void
     */
    public function testDeletePermissionAssigns()
    {
        $user = User::firstOrCreate($this->userData);
        $testRole = Role::firstOrCreate(['name' => $this->roleName]);
        $permission = Permission::firstOrCreate(['name' => 'permissions.assigns.delete']);
        RolePerms::grantPermission($this->roleName, 'permissions.assigns.delete');
        RolePerms::grantRole($user, $this->roleName);

        $permissionAssign = DB::table('role_permissions')->where([
            'role_id' => $testRole->id,
            'permission_id' => $permission->id
        ])->first();

        $permissionAssignId = $permissionAssign->id;

        $this->assertIsNumeric($permissionAssignId);

        $response = $this->actingAs($user, 'api')
                    ->call('DELETE', '/api/permission_assigns/'.$permissionAssignId);

        $this->assertEquals(204, $response->status());
        $permissionAssign = DB::table('role_permissions')->where(['id' => $permissionAssignId])->first();
        $this->assertNull($permissionAssign);

        $testRole->permissions()->sync([]);
        RolePerms::revokeRole($user, $this->roleName);
        RolePerms::deleteRole($this->roleName);
    }

}
