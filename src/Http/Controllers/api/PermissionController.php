<?php

namespace kevinberg\LaravelRolePerms\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use kevinberg\LaravelRolePerms\Models\Permission;
use kevinberg\LaravelRolePerms\Facades\RolePerms;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::check() && RolePerms::hasPermission('show_perms')) {
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
        if(Auth::check() && RolePerms::hasPermission('create_perms')) {

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
        if(Auth::check() && RolePerms::hasPermission('show_perms')) {

            if(empty($id) || ! is_numeric($id)) {
                return abort(404);
            }

            $permission = Permission::findOrFail($id);
            return response()->json($permission, 201);
        }

        return response(null, 401);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(Auth::check() && RolePerms::hasPermission('edit_perms')) {

            $request->validate([
                'name' => 'required|unique:permissions'
            ]);

            $permission = Permission::findOrFail($id);
            $permission->name = $request->name;
            $saved = $permission->save();

            if($saved) {
                return response()->json($permission, 201);
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
        if(Auth::check() && RolePerms::hasPermission('delete_perms')) {
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
