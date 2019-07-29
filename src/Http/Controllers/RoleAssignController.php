<?php

namespace Oncampus\LaravelRolePerms\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Oncampus\LaravelRolePerms\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
            return view('LaravelRolePerms::role_assigns', [
                'roleAssigns' => $roleAssigns
            ]);

        }

        return redirect(config('role_perms.redirect_route_on_fail'));
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

            return view('LaravelRolePerms::role_assign', [
                'roleAssign' => $roleAssign,
                'user' => $user,
                'role' => $role
            ]);

        }

        return redirect(config('role_perms.redirect_route_on_fail'));
    }

    /**
     * Updates a role assignment.
     *
     * @param Request $request
     * @param integer $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,int $id)
    {
        if(Auth::check() && Auth::user()->hasPermission('roles.assigns.edit')) {

            $request->validate([
                'entity_type' => 'string|nullable',
                'entity_id' => 'integer|nullable'
            ]);

            $roleAssign = DB::table('role_assign')->where('id', $id)->first();

            if($roleAssign === null) {
                abort(404);
            }

            DB::table('role_assign')->where('id', $id)->update([
                'entity_type' => $request->entity_type,
                'entity_id' => $request->entity_id
            ]);

            return redirect()->route('role_assigns.show', [
                'id' => $id
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
        if(Auth::check() && Auth::user()->hasPermission('roles.assigns.delete')) {

            DB::table('role_assign')->where('id', $id)->delete();
            return redirect()->route('role_assigns.index');

        }

        return redirect(config('role_perms.redirect_route_on_fail'));
    }
}
