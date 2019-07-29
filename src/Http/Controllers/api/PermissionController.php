<?php

namespace Oncampus\LaravelRolePerms\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Oncampus\LaravelRolePerms\Models\Permission;
use Oncampus\LaravelRolePerms\Facades\RolePerms;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::check() && Auth::user()->hasPermission('permissions.show')) {
            $permissions = Permission::all();
            return response()->json($permissions, 200);
        }

        return response(null, 401);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Auth::check() && Auth::user()->hasPermission('permissions.create')) {

            $request->validate([
                'name' => 'required|unique:permissions'
            ]);

            $permission = RolePerms::createPermission($request->name);

            if($permission !== null) {
                return response()->json($permission, 201);
            } else {
                return response(null, 422);
            }
        }

        return response(null, 401);
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(Auth::check() && Auth::user()->hasPermission('permissions.show')) {

            if(empty($id) || ! is_numeric($id)) {
                return abort(404);
            }

            $permission = Permission::findOrFail($id);
            return response()->json($permission, 201);
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
        if(Auth::check() && Auth::user()->hasPermission('permissions.delete')) {
            $permission = Permission::findOrFail($id);
            $deleted = $permission->delete();

            if($deleted) {
                return response(null, 204);
            } else {
                return response(null, 422);
            }
        }

        return response(null, 401);
    }
}
