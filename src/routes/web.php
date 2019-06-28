<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(['namespace' => 'kevinberg\LaravelRolePerms\Http\Controllers'], function() {
        Route::resource('roles', 'RoleController')->only([
            'index', 'store', 'show', 'update', 'destroy'
        ]);
        Route::resource('permissions', 'PermissionController')->only([
            'index', 'store', 'show', 'update', 'destroy'
        ]);
        Route::get('role_assign/{id}', 'RoleAssignController@showRoleAssign')->name('roles.assign.show');
        Route::patch('role_assign/{id}', 'RoleAssignController@updateRoleAssign')->name('roles.assign.update');
        Route::delete('role_assign/{id}', 'RoleAssignController@destroyRoleAssign')->name('roles.assign.delete');
    });
});
