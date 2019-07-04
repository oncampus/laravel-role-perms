<?php

namespace kevinberg\LaravelRolePerms\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use kevinberg\LaravelRolePerms\Models\Role;
use kevinberg\LaravelRolePerms\Facades\RolePerms;
use Illuminate\Support\Facades\DB;

class RoleAssignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::check() && Auth::user()->hasPermission('roles.assigns.show')) {
            $roleAssigns = DB::table('role_assign')->get();
            return response()->json($roleAssigns, 200);
        }

        return response(null, 401);
    }

    /**
     * Display the specified roleAssign resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        if(Auth::check() && Auth::user()->hasPermission('roles.assigns.show')) {

            $roleAssign = DB::table('role_assign')->where('id', $id)->first();
            if($roleAssign === null) {
                abort(404);
            }

            $user = User::where('id', $roleAssign->user_id)->first();
            $role = Role::where('id', $roleAssign->role_id)->first();

            return response()->json([
                'roleAssign' => $roleAssign,
                'user' => $user,
                'role' => $role
            ], 200);
        }

        return response(null, 401);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        if(Auth::check() && Auth::user()->hasPermission('roles.assigns.create')) {

            $user = User::where('id', $request->user_id)->first();
            if($user === null) {
                abort(404, 'User not found.');
            }

            $result = RolePerms::grantRole($user, $request->role);

            if($result) {
                return response(null, 201);
            } else {
                return response(null, 422);
            }
        }

        return response(null, 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Auth::check() && Auth::user()->hasPermission('roles.assigns.delete')) {
            DB::table('role_assign')->where('id', $id)->delete();
            return response(null, 204);
        }

        return response(null, 401);
    }
}
