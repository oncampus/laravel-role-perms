@extends('layouts.app')

@section('title', 'Page Title')

@section('sidebar')
    @parent
@endsection

@section('content')

    <h1>Laravel Role Perms</h1>
    <h2>Permissions</h2>

    @foreach ($permissions as $permission)
        <p>
            <a href="{{ route('permissions.show', $permission ) }}">
                id: {{ $permission->id }} name: {{ $permission->name }}
            </a>
        </p>
    @endforeach

    <hr />

    <h2>New Role</h2>

    <form action="{{ route('permissions.store') }}" method="post">
        @csrf
        <input type="text" name="name" placeholder="Permission name" />
        <input type="submit" />
    </form>

@endsection