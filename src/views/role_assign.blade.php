@extends('layouts.app')

@section('title', 'Page Title')

@section('sidebar')
    @parent
@endsection

@section('content')

    <h1>Laravel Role Perms</h1>

    @include('LaravelRolePerms::navigation')

    <h2>Role Assignment (<a href="{{ route('roles.show', [$role->id]) }}">{{ $role->name }}</a>)</h2>

    <form action="{{ route('roles.assign.update', [$roleAssign->id]) }}" method="post">

        @csrf
        @method('PATCH')

        <div>
            <label for="entity_type">Type</label>
            <input type="text" name="entity_type" placeholder="Type" value="{{ $roleAssign->entity_type }}"/>
        </div>

        <div>
            <label for="entity_id">Type</label>
            <input type="number" name="entity_id" placeholder="ID" value="{{ $roleAssign->entity_id }}"/>
        </div>

        <input type="submit" />
    </form>

    <hr />

    <form action="{{ route('roles.assign.delete', [$roleAssign->id]) }}" method="post">
        @csrf
        @method('DELETE')
        <input type="submit" value="Delete"/>
    </form>

@endsection