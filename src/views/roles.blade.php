@extends('layouts.app')

@section('title', 'Page Title')

@section('sidebar')
    @parent
@endsection

@section('content')

    <h1>Laravel Role Perms</h1>
    <h2>Roles</h2>

    @foreach ($roles as $role)
        <p>
            <a href="{{ route('roles.show', $role ) }}">
                id: {{ $role->id }} name: {{ $role->name }}
            </a>
        </p>
    @endforeach

    <hr />

    <h2>New Role</h2>

    <form action="{{ route('roles.store') }}" method="post">
        @csrf
        <input type="text" name="name" placeholder="Role name" />
        <input type="submit" />
    </form>

@endsection