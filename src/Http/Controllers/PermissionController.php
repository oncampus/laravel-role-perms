<?php

namespace kevinberg\LaravelRolePerms\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use kevinberg\LaravelRolePerms\Models\Permission;
use Illuminate\Support\Facades\Auth;

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
            return view('LaravelRolePerms::permissions', [
                'permissions' => $permissions
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
        if(Auth::check() && Auth::user()->hasPermission('permissions.create')) {

            $request->validate([
                'name' => 'required|unique:permissions'
            ]);

            $permission = new Permission();
            $permission->name = $request->name;
            $permission->save();

            return redirect()->route('permissions.index');

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
        if(Auth::check() && Auth::user()->hasPermission('permissions.show')) {

            if(empty($id) || ! is_numeric($id)) {
                return abort(404);
            }

            $permission = Permission::findOrFail($id);

            return view('LaravelRolePerms::permission', [
                'permission' => $permission
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
        if(Auth::check() && Auth::user()->hasPermission('permissions.delete')) {

            $permission = Permission::findOrFail($id);
            $permission->delete();
            return redirect()->route('permissions.index');

        }

        return redirect(config('role_perms.redirect_route_on_fail'));
    }
}
