@extends('layouts.app')

@section('title', 'Page Title')

@section('sidebar')
    @parent
@endsection

@section('content')

    <h1>Laravel Role Perms - Show Permission</h1>

    @include('LaravelRolePerms::navigation')

    <hr />

    <h2>Permission name: {{ $permission->name }}</h2>
    <h3>id: {{ $permission->id }}</h3>

    <form action="{{ route('permissions.update', [$permission]) }}" method="post">
        @csrf
        @method('PATCH')
        <input type="text" name="name" placeholder="Permission name" value="{{ $permission->name }}"/>
        <input type="submit" value="save"/>
    </form>

    <hr />

    <h2>Used by roles</h2>

    @foreach ($permission->roles as $role)
        <ul>
            <li>
                <a href="{{ route('roles.show', $role ) }}">
                    id: {{ $role->id }} name: {{ $role->name }}
                </a>
            </li>
        </ul>
    @endforeach

    <hr />

    <form action="{{ route('permissions.destroy', [$permission]) }}" method="post">
        @csrf
        @method('DELETE')
        <input type="submit" value="Delete"/>
    </form>

@endsection