<?php

namespace bedoke\LaravelRolePerms\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use bedoke\LaravelRolePerms\Models\Role;
use bedoke\LaravelRolePerms\Models\Permission;
use bedoke\LaravelRolePerms\Facades\RolePerms;
use Illuminate\Support\Facades\Auth;

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
            return view('LaravelRolePerms::roles', [
                'roles' => $roles
            ]);
        }

        return redirect(config('role_perms.redirect_route_on_fail'));
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

            $role = new Role();
            $role->name = $request->name;
            $role->save();

            return redirect()->route('roles.index');
        }

        return redirect(config('role_perms.redirect_route_on_fail'));
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
            $permissions = Permission::all();
            $users = User::all();

            return view('LaravelRolePerms::role', [
                'role' => $role,
                'permissions' => $permissions,
                'users' => $users
            ]);
        }

        return redirect(config('role_perms.redirect_route_on_fail'));
    }


    /**
     * Update the specified resource in storage.
     * TODO remove this route and function!
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if( Auth::check() &&
            Auth::user()->hasPermission('roles.edit') &&
            Auth::user()->hasPermission('roles.assigns.create') &&
            Auth::user()->hasPermission('permissions.assigns.create')) {

            $request->validate([
                'permissions' => 'array',
                'permissions.*' => 'exists:permissions,id',
                'users' => 'array',
                'users.*' => 'exists:users,id',
                'responsible_users' => 'array',
                'responsible_users.*' => 'exists:users,id'
            ]);

            $role = Role::findOrFail($id);
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

        return redirect(config('role_perms.redirect_route_on_fail'));
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
            $role->delete();

            return redirect()->route('roles.index');
        }

        return redirect(config('role_perms.redirect_route_on_fail'));
    }
}
