<?php

Route::group(['namespace' => 'kevinberg\LaravelRolePerms\Http\Controllers'], function() {
    /* Route::get('roles', 'RoleController@index')->name('show_roles');
    Route::post('roles', 'RoleController@create')->name('create_role');
    Route::get('roles/{id}', 'RoleController@show')->name('show_role');
    Route::post('roles/{id}', 'RoleController@show')->name('update_role'); */
    Route::resources([
        'roles' => 'RoleController'
    ]);
});
