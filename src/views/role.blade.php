@extends('layouts.app')

@section('title', 'Page Title')

@section('sidebar')
    @parent
@endsection

@section('content')

    <h1>Laravel Role Perms - Show Role</h1>

    @include('LaravelRolePerms::navigation')

    <hr />

    <h2>Role name: {{ $role->name }}</h2>
    <h3>id: {{ $role->id }}</h3>

    <form action="{{ route('roles.update', [$role]) }}" method="post">
        @csrf
        @method('PATCH')

        <div>
            <label for="name">Role name</label>
            <input type="text" name="name" placeholder="Role name" value="{{ $role->name }}"/>
        </div>

        <div>
            <label for="permissions">Permissions</label>
            <select multiple id="permissions" name="permissions[]">
                @foreach($permissions as $permission)
                    @if($role->permissions->contains($permission->id))
                        <option value="{{ $permission->id }}" selected>{{ $permission->name }}</option>
                    @else
                        <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                    @endif
                @endforeach
            </select>
        </div>

        <div>
            <label for="users">User</label>
            <select multiple id="users" name="users[]">
                @foreach($users as $user)
                    @if($role->users->contains($user->id))
                        <option value="{{ $user->id }}" selected>{{ $user->name }}</option>
                    @else
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endif
                @endforeach
            </select>
        </div>

        <div>
            <label for="responsible_users">Responsible User</label>
            <select multiple id="responsible_users" name="responsible_users[]">
                @foreach($users as $user)
                    @if($role->responsibleUsers->contains($user->id))
                        <option value="{{ $user->id }}" selected>{{ $user->name }}</option>
                    @else
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endif
                @endforeach
            </select>
        </div>

        <input type="submit" value="save"/>
    </form>

    <hr />

    <h2>Role Assignments</h2>

    <table>
        <tr>
            <th>#ID</th>
            <th>User</th>
            <th>Entity Type</th>
            <th>Entity ID</th>
        </tr>
        @foreach($role->users as $user)
            <tr>
                <td>
                    <a href="{{ route('role_assigns.show', [$user->pivot->id]) }}">{{ $user->pivot->id }}</a>
                </td>
                <td>
                    {{ $user->name }}
                </td>
                <td>
                    {{ $user->pivot->entity_type }}
                </td>
                <td>
                    {{ $user->pivot->entity_id }}
                </td>
            </tr>
        @endforeach
    </table>

    <hr />

    <form action="{{ route('roles.destroy', [$role]) }}" method="post">
        @csrf
        @method('DELETE')
        <input type="submit" value="Delete"/>
    </form>

@endsection