<?php

namespace kevinberg\LaravelRolePerms\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use kevinberg\LaravelRolePerms\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleAssignController extends Controller
{
    /**
     * Display the specified roleAssign resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function showRoleAssign(int $id)
    {
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

    /**
     * Updates a role assignment.
     *
     * @param Request $request
     * @param integer $id
     * @return \Illuminate\Http\Response
     */
    public function updateRoleAssign(Request $request,int $id)
    {
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

        return redirect()->route('roles.assign.show', ['id' => $id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyRoleAssign($id)
    {
        DB::table('role_assign')->where('id', $id)->delete();
        return redirect()->route('roles.index');
    }
}