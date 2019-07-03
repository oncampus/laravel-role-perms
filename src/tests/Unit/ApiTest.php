<?php

namespace kevinberg\LaravelRolePerms\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use kevinberg\LaravelRolePerms\Facades\RolePerms;
use kevinberg\LaravelRolePerms\Models\Role;
use Illuminate\Support\Facades\Cache;
use App\User;

use Illuminate\Support\Facades\Auth;

class ApiTest extends TestCase
{
    /* TODO */
    public function testApiRoutes()
    {
        /* Cache::flush();
        $testUser = factory(User::class)->create();
        $response = $this->actingAs($testUser)->call('GET', '/api/permissions/');
        $this->assertEquals(200, $response->status());
        $testUser->delete(); */
    }
}
