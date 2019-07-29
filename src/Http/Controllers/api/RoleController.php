<?php

namespace Oncampus\LaravelRolePerms\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Oncampus\LaravelRolePerms\Models\Role;
use Oncampus\LaravelRolePerms\Facades\RolePerms;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::check() && Auth::user()->hasPermission('roles.show')) {
            $roles = Role::all();
            return response()->json($roles, 200);
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
        if(Auth::check() && Auth::user()->hasPermission('roles.create')) {

            $request->validate([
                'name' => 'required|unique:roles'
            ]);

            $role = RolePerms::createRole($request->name);

            if($role !== null) {
                return response()->json($role, 201);
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
        if(Auth::check() && Auth::user()->hasPermission('roles.show')) {

            if(empty($id) || ! is_numeric($id)) {
                return abort(404);
            }

            $role = Role::findOrFail($id);
            return response()->json($role, 201);
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
        if(Auth::check() && Auth::user()->hasPermission('roles.delete')) {
            $role = Role::findOrFail($id);
            $deleted = $role->delete();

            if($deleted) {
                return response(null, 204);
            } else {
                return response(null, 422);
            }
        }

        return response(null, 401);
    }
}
