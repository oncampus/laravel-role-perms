<?php

namespace kevinberg\LaravelRolePerms\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use kevinberg\LaravelRolePerms\Models\Role;
use kevinberg\LaravelRolePerms\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();
        return view('LaravelRolePerms::roles', [
            'roles' => $roles
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles'
        ]);

        $role = new Role();
        $role->name = $request->name;
        $role->save();

        return redirect()->route('roles.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(empty($id) || ! is_numeric($id)) {
            return abort(404);
        }

        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $users = User::all();

        return view('LaravelRolePerms::role', [
            'role' => $role,
            'permissions' => $permissions,
            'users' => $users
        ]);
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
        $request->validate([
            'name' => 'string|min:3|unique:permissions',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
            'users' => 'array',
            'users.*' => 'exists:users,id',
            'responsible_users' => 'array',
            'responsible_users.*' => 'exists:users,id'
        ]);

        $role = Role::findOrFail($id);
        $role->name = $request->name;
        $role->permissions()->sync($request->permissions);
        $role->users()->sync($request->users);
        $role->responsibleUsers()->sync($request->responsible_users);
        $role->save();

        $permissions = Permission::all();
        $users = User::all();

        return view('LaravelRolePerms::role', [
            'role' => $role,
            'permissions' => $permissions,
            'users' => $users
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('roles.index');
    }
}