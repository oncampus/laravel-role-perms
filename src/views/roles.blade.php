@extends('layouts.app')

@section('title', 'Page Title')

@section('sidebar')
    @parent
@endsection

@section('content')

    <h1>Laravel Role Perms</h1>

    @include('LaravelRolePerms::navigation')

    <h2>Roles</h2>

    @foreach ($roles as $role)
        <ul>
            <li>
                <a href="{{ route('roles.show', $role ) }}">
                    id: {{ $role->id }} name: {{ $role->name }}
                </a>
            </li>
        </ul>
    @endforeach

    <hr />

    <h2>New Role</h2>

    <form action="{{ route('roles.store') }}" method="post">
        @csrf
        <input type="text" name="name" placeholder="Role name" />
        <input type="submit" />
    </form>

@endsection