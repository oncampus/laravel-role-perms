<?php

return [
    /**
     * Redirect unauthorized users to this route (web guard).
     * Used for routes like /roles or /permissions.
     */
    'redirect_route_on_fail' => '/login',

    /**
     * Regex patterns for role and permission names.
     */
    'permission_name_pattern' => '/^[a-zA-Z0-9-_. ]+$/',
    'role_name_pattern' => '/^[a-zA-Z0-9-_. ]+$/',

    /**
     * Cache keys for calculated roles and permissions.
     */
    'roles_cache_key' => 'roles',
    'perms_cache_key' => 'perms'
];
