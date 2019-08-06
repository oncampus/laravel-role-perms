# Install

	composer require oncampus/model-repositories

## Extend the user class



Import the Roles trait in the User.php file:



    use Oncampus\LaravelRolePerms\Traits\Roles;



Inside the user class use the trait:



    use Roles;

## Migration

    php artisan migrate

## Seeds



    php artisan db:seed --class=Oncampus\LaravelRolePerms\Database\Seeds



By default the following seeder creates the admin role and some permission. If there is a user with the name admin, then by default he gets the role admin.



# Usage



## Functions



### User Model

To check if a user model has a permission or a role you can use this:

    $user->hasPermission('permissionName', [Object $entity]); // true || false
    $user->hasRole('roleName', [Object $entity]); // true || false

The user function results will be cached!
Use `RolePerms::clearPermissionCache([User $user]);` or `RolePerms::clearRoleCache([User $user]);` to clean up if needed.

### Facade

Include the Facade to use the following functions.

    use Oncampus\LaravelRolePerms\Facades\RolePerms;

Now you can use the following functions:

    RolePerms::userHasRole(User  $user, String  $roleName, [Object $entity]); // true || false

    RolePerms::userHasPermission(User  $user, String  $permissionName, [Object $entity]); // true || false

	RolePerms::roleHasPermission(String  $roleName, String  $permissionName); // true || false

	RolePerms::grantRole(User  $user, String  $roleName, [Object $entity]); // true || false

	RolePerms::grantPermission(String  $roleName, String  $permissionName); // true || false

	RolePerms::revokeRole(User  $user, String  $roleName, [Object $entity]); // true || false

	RolePerms::revokePermission(String  $roleName, String  $permissionName); // true || false

	RolePerms::clearRoleCache([User $user]); // true || false

	RolePerms::clearPermissionCache([User $user]); // true || false

	RolePerms::createRole(String  $roleName); // Role || false

	RolePerms::createPermission(String  $permissionName); // Permission || false

	RolePerms::deleteRole(String  $roleName); // true || false

	RolePerms::deletePermission(String  $permissionName); // true || false
