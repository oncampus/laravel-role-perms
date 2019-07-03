<?php

namespace kevinberg\LaravelRolePerms\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use kevinberg\LaravelRolePerms\Facades\RolePerms;
use Illuminate\Support\Facades\Cache;
use App\User;

class RoleAssignTest extends TestCase
{
    public function testCreateAssignAndDeleteRole()
    {
        Cache::flush();
        $testUser = factory(User::class)->create();
        $roleName = 'testRole_'.uniqid();
        $permissionName = 'testPerm_'.uniqid();

        # CREATE ROLE
        $createdRole = RolePerms::createRole($roleName);
        $this->assertInstanceOf('kevinberg\LaravelRolePerms\Models\Role', $createdRole);

        # GRANT ROLE
        $grantRole = RolePerms::grantRole($testUser, $roleName);
        $this->assertTrue($grantRole);

        # USER HAS ROLE?
        $hasRole = RolePerms::userHasRole($testUser, $roleName);
        $this->assertTrue($hasRole);

        # CREATE PERMISSION
        $createdPermission = RolePerms::createPermission($permissionName);
        $this->assertInstanceOf('kevinberg\LaravelRolePerms\Models\Permission', $createdPermission);

        # GRANT PERMISSION TO ROLE
        $grantPerm = RolePerms::grantPermission($roleName, $permissionName);
        $this->assertTrue($grantPerm);

        # HAS PERMISSION?
        $hasPerm = RolePerms::roleHasPermission($roleName, $permissionName);
        $this->assertTrue($hasPerm);

        # REVOKE PERMISSION
        $revokedPerm = RolePerms::revokePermission($roleName, $permissionName);
        $this->assertTrue($revokedPerm);

        # REVOKE ROLE
        $revokedRole = RolePerms::revokeRole($testUser, $roleName);
        $this->assertTrue($revokedRole);

        # USER STILL HAS ROLE?
        $hasRole = RolePerms::userHasRole($testUser, $roleName);
        $this->assertFalse($hasRole);

        # DELETE ROLE
        $deletedRole = RolePerms::deleteRole($roleName);
        $this->assertTrue($deletedRole);

        $testUser->delete();
    }
}
