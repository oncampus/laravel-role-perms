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

class ApiTest extends TestCase
{
    private $roleName = 'testRole';

    private $userData = [
        'name' => 'testUser',
        'email' => 'test.user@has.no.mail',
        'password' => 'supersafepassword'
    ];

    public function testShowPermissions()
    {
        Cache::flush();
        $user = User::firstOrCreate($this->userData);

        $userHasRole = RolePerms::userHasRole($user, $this->roleName);
        $this->assertIsBool($userHasRole);

        if(!$userHasRole) {
            Role::firstOrCreate(['name' => $this->roleName]);
            Permission::firstOrCreate(['name' => 'permissions.show']);
            RolePerms::grantPermission($this->roleName, 'permissions.show');
            RolePerms::grantRole($user, $this->roleName);
        }

        $response = $this->actingAs($user, 'api')->call('GET', '/api/permissions/');
        $this->assertEquals(200, $response->status());
        RolePerms::revokeRole($user, $this->roleName);
        Cache::flush();
    }

}
