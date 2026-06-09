@extends('layouts.app')

@section('content')

@if (session('error'))
<div class="alert"></div>
    {{ session('error') }}
</div>
@endif

<form action="{{ route('users.store') }}" method="POST">
    @csrf
    @method('POST')
    <!-- Form fields for creating user -->

    <label for="name">Nome:</label>
    <input type="text" id="name" name="name" value="" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="" required>

    <label for="role">Role:</label>
    <select id="role" name="role" required>
        <option value="admin">Admin</option>
        <option value="user">User</option>
    </select>

    <button type="submit">Criar Usuário</button>

</form>


@endsection