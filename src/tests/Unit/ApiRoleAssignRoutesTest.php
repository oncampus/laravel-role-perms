<?php

namespace Oncampus\LaravelRolePerms\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Oncampus\LaravelRolePerms\Facades\RolePerms;
use Oncampus\LaravelRolePerms\Models\Role;
use Oncampus\LaravelRolePerms\Models\Permission;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\User;

class ApiRoleAssignRoutesTest extends TestCase
{
    private $roleName = 'testRole';

    private $userData = [
        'name' => 'testUser',
        'email' => 'test.user@has.no.mail',
        'password' => 'supersafepassword'
    ];

    /**
     * Tests the index route for roles assigns.
     *
     * @return void
     */
    public function testShowRoleAssigns()
    {
        Cache::flush();
        $user = User::firstOrCreate($this->userData);

        $testRole = Role::firstOrCreate(['name' => $this->roleName]);
        Permission::firstOrCreate(['name' => 'roles.assigns.show']);
        RolePerms::grantPermission($this->roleName, 'roles.assigns.show');
        RolePerms::grantRole($user, $this->roleName);

        $response = $this->actingAs($user, 'api')->call('GET', 'api/role_assigns');
        $this->assertEquals(200, $response->status());
        $testRole->permissions()->sync([]);
        RolePerms::revokeRole($user, $this->roleName);
        RolePerms::deleteRole($this->roleName);
        Cache::flush();
    }

    /**
     * Tests the store route for roles assigns.
     *
     * @return void
     */
     public function testCreateRoleAssigns()
    {
        $user = User::firstOrCreate($this->userData);

        $testRole = Role::firstOrCreate(['name' => $this->roleName]);
        Permission::firstOrCreate(['name' => 'roles.assigns.create']);
        RolePerms::grantPermission($this->roleName, 'roles.assigns.create');
        RolePerms::grantRole($user, $this->roleName);

        $response = $this->actingAs($user, 'api')
                    ->call('POST', '/api/role_assigns/', [
                        'user_id' => $user->id,
                        'role_name' => $this->roleName,
                        'entity_type' => 'Great Entity!', # Todo test!
                        'entity_id' => '777' # Todo test!
                    ]);

        $this->assertEquals(201, $response->status());
        $testRole->permissions()->sync([]);
        RolePerms::revokeRole($user, $this->roleName);
        RolePerms::deleteRole($this->roleName);
    }

    /**
     * Tests the destroy route for roles assigns.
     *
     * @return void
     */
     public function testDeleteRoleAssigns()
    {
        $user = User::firstOrCreate($this->userData);
        $testRole = Role::firstOrCreate(['name' => $this->roleName]);
        Permission::firstOrCreate(['name' => 'roles.assigns.delete']);
        RolePerms::grantPermission($this->roleName, 'roles.assigns.delete');
        RolePerms::grantRole($user, $this->roleName);

        $role = $user->roles->where('name', $this->roleName)->first();
        $roleAssignId = $role->pivot->id;

        $response = $this->actingAs($user, 'api')
                    ->call('DELETE', '/api/role_assigns/'.$roleAssignId);

        $this->assertEquals(204, $response->status());
        $roleAssign = DB::table('role_assign')->where('id', $roleAssignId)->first();
        $this->assertNull($roleAssign);

        $testRole->permissions()->sync([]);
        RolePerms::deleteRole($this->roleName);
    }

}
