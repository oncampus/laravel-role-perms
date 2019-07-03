<?php

Route::group(['middleware' => ['api']], function () {
    Route::group(['namespace' => 'kevinberg\LaravelRolePerms\Http\Controllers\Api'], function() {
        /* Route::resource('roles', 'RoleController')->only([
            'index', 'store', 'show', 'update', 'destroy'
        ]);
        Route::resource('permissions', 'PermissionController')->only([
            'index', 'store', 'show', 'update', 'destroy'
        ]);
        Route::resource('role_assigns', 'RoleAssignController')->only([
            'index', 'store', 'show', 'update', 'destroy'
        ]); */
    });
});
