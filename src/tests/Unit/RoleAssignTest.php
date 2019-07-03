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

        # CREATE ROLE
        $createdRole = RolePerms::createRole($roleName);
        $this->assertInstanceOf('kevinberg\LaravelRolePerms\Models\Role', $createdRole);

        # GRANT ROLE
        $assignedRole = RolePerms::grantRole($testUser, $roleName);
        $this->assertTrue($assignedRole);

        # USER HAS ROLE?
        $hasRole = RolePerms::userHasRole($testUser, $roleName);
        $this->assertTrue($hasRole);

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
