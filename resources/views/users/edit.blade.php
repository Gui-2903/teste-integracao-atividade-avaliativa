@extends('layouts.app')

@section('content')

@if (session('message'))
<div class="alert"></div>
    {{ session('message') }}
</div>
@endif

<form action="{{ route('users.update', ['user' => $user->id]) }}" method="POST">
    @csrf
    @method('PUT')
    <!-- Form fields for editing user -->

    <label for="name">Nome:</label>
    <input type="text" id="name" name="name" value="{{ $user->name }}" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="{{ $user->email }}" required>

    <label for="role">Role:</label>
    <select id="role" name="role" required>
        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
        <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
    </select>

    <button type="submit">Atualizar Usuário</button>

</form>
@endsection