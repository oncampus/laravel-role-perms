@extends('layouts.app')

@section('title', 'Page Title')

@section('sidebar')
    @parent
@endsection

@section('content')

    <h1>Laravel Role Perms</h1>

    @include('LaravelRolePerms::navigation')

    <h2>Permissions</h2>

    @foreach ($permissions as $permission)
        <ul>
            <li>
                <a href="{{ route('permissions.show', $permission ) }}">
                    id: {{ $permission->id }} name: {{ $permission->name }}
                </a>
            </li>
        </ul>
    @endforeach

    <hr />

    <h2>New Permission</h2>

    <form action="{{ route('permissions.store') }}" method="post">
        @csrf
        <input type="text" name="name" placeholder="Permission name" />
        <input type="submit" />
    </form>

@endsection