<?php

return [
    'redirect_route_on_fail' => '/login', # redirect unauthorized users to this route (web guard).

    'permission_name_pattern' => '/^[a-zA-Z0-9-_. ]+$/',
    'role_name_pattern' => '/^[a-zA-Z0-9-_. ]+$/',

    'roles_cache_key' => 'roles',
    'perms_cache_key' => 'perms'
];
