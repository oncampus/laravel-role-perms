<?php

namespace Oncampus\LaravelRolePerms\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Oncampus\LaravelRolePerms\Models\Permission;
use Oncampus\LaravelRolePerms\Facades\RolePerms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PermissionAssignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::check() && Auth::user()->hasPermission('permissions.assigns.show')) {
            $permissionAssigns = DB::table('role_permissions')->get();
            return response()->json($permissionAssigns, 200);
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
        if(Auth::check() && Auth::user()->hasPermission('permissions.assigns.create')) {

            $request->validate([
                'role_name' => 'string|required|exists:roles,name',
                'permission_name' => 'string|required|exists:permissions,name',
            ]);

            $result = RolePerms::grantPermission($request->role_name, $request->permission_name);

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
        if(Auth::check() && Auth::user()->hasPermission('permissions.assigns.delete')) {

            if(!is_numeric($id)) {
                abort(400);
            }

            $deleted = DB::table('role_permissions')->where(['id' => $id])->delete();

            if($deleted) {
                return response(null, 204);
            } else {
                return response(null, 422);
            }

        }

        return response(null, 401);
    }
}
