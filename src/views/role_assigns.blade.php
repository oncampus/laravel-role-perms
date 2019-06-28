@extends('layouts.app')

@section('title', 'Page Title')

@section('sidebar')
    @parent
@endsection

@section('content')

    <h1>Laravel Role Perms</h1>

    @include('LaravelRolePerms::navigation')

    <h2>Role Assignments</h2>

    <table>
        <tr>
            <th>#ID</th>
            <th>User</th>
            <th>Entity Type</th>
            <th>Entity ID</th>
        </tr>
        @foreach ($roleAssigns as $roleAssign)
            <tr>
                <td>
                    <a href="{{ route('role_assigns.show', [$roleAssign->id] ) }}">{{ $roleAssign->id }}</a>
                </td>
                <td>
                    {{ $roleAssign->user_id }}
                </td>
                <td>
                    {{ $roleAssign->entity_type }}
                </td>
                <td>
                    {{ $roleAssign->entity_id }}
                </td>
            </tr>
        @endforeach
    </table>

@endsection