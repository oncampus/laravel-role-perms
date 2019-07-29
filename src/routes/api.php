<?php
Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'api',
    'namespace' => 'Oncampus\LaravelRolePerms\Http\Controllers\Api'],
    function() {
        Route::resource('roles', 'RoleController')->only([
            'index', 'store', 'show', 'destroy'
        ]);
        Route::resource('permissions', 'PermissionController')->only([
            'index', 'store', 'show', 'destroy'
        ]);
        Route::resource('role_assigns', 'RoleAssignController')->only([
            'index', 'store', 'destroy'
        ]);
        Route::resource('permission_assigns', 'PermissionAssignController')->only([
            'index', 'store', 'destroy'
        ]);
});
