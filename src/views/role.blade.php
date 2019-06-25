@extends('layouts.app')

@section('title', 'Page Title')

@section('sidebar')
    @parent
@endsection

@section('content')

    <h1>Laravel Role Perms - Show Role</h1>
    <h2>id: {{ $role->id }}</h2>

    <form action="{{ route('roles.update', [$role]) }}" method="post">
        @csrf
        @method('PATCH')
        <input type="text" name="name" placeholder="Role name" value="{{ $role->name }}"/>
        <input type="submit" />
    </form>

    <hr />

    <form action="{{ route('roles.destroy', [$role]) }}" method="post">
        @csrf
        @method('DELETE')
        <input type="submit" value="Delete"/>
    </form>

@endsection