@extends('layouts.app')

@section('title', 'Page Title')

@section('sidebar')
    @parent
@endsection

@section('content')

    <h1>Laravel Role Perms - Show Permission</h1>
    <h2>id: {{ $permission->id }}</h2>

    <form action="{{ route('permissions.update', [$permission]) }}" method="post">
        @csrf
        @method('PATCH')
        <input type="text" name="name" placeholder="Permission name" value="{{ $permission->name }}"/>
        <input type="submit" />
    </form>

    <hr />

    <form action="{{ route('permissions.destroy', [$permission]) }}" method="post">
        @csrf
        @method('DELETE')
        <input type="submit" value="Delete"/>
    </form>

@endsection