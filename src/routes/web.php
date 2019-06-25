<?php

Route::group(['namespace' => 'kevinberg\LaravelRolePerms\Http\Controllers'], function() {
    Route::resources([
        'roles' => 'RoleController',
        'permissions' => 'PermissionController'
    ]);
});
