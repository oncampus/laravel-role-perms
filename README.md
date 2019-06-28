# Install
## Get the package

    composer require kevinberg/laravel-role-perms

## Extend the user class

Import the Roles trait in the User.php file:

    use kevinberg\LaravelRolePerms\Traits\Roles;

Inside the user class use the trait:

    use Roles;

## Seeds

    php artisan db:seed --class=kevinberg\LaravelRolePerms\Database\Seeds

By default the following seeder creates the admin role and the * permission. If there is a user with the name admin, then by default he gets the role admin.
