@extends('layouts.app')

@section('content')
@if (session('message'))
<div class="alert"></div>
    {{ session('message') }}
</div>
@endif

@if (session('error'))
<div class="alert"></div>
    {{ session('error') }}
</div>
@endif

<a href="{{ route('users.create') }}">Criar Novo Usuário</a>

<table>
    <thead>
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Role</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
            <tr>
                <td><a href="{{ route('users.show', ['user' => $user->id]) }}">{{ $user->name }}</a></td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role }}</td>
            </tr>
        @endforeach
    </tbody>
</table>


@endsection
