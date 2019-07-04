<?php
Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'api',
    'namespace' => 'kevinberg\LaravelRolePerms\Http\Controllers\Api'],
    function() {
        Route::resource('roles', 'RoleController')->only([
            'index', 'store', 'show', 'destroy'
        ]);
        Route::resource('permissions', 'PermissionController')->only([
            'index', 'store', 'show', 'destroy'
        ]);
        /* Route::resource('role_assigns', 'RoleAssignController')->only([
            'index', 'store', 'show', 'update', 'destroy'
        ]); */
});
