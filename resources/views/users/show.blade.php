@extends('layouts.app')

@section('content')

@if (session('message'))
<div class="alert"></div>
    {{ session('message') }}
</div>
@endif

<form>
    @csrf
    @method('PUT')
    <!-- Form fields for editing user -->

    <label for="name">Nome:</label>
    <input type="text" id="name" name="name" value="{{ $user->name }}" disabled>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="{{ $user->email }}" disabled>

    <label for="role">Role:</label>
    <select id="role" name="role" disabled>
        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
        <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
    </select>

    <hr>

    <h4>Bibliotecas</h4>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Endereço</th>
                <th>Telefone</th>
                <th>Email</th>
                <th>Created at</th>
                <th>Updated at</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($user->bibliotecas as $biblioteca)
                <tr>
                    <td>{{ $biblioteca->nome }}</td>
                    <td>{{ $biblioteca->endereco }}</td>
                    <td>{{ $biblioteca->telefone }}</td>
                    <td>{{ $biblioteca->email }}</td>
                    <td>{{ $biblioteca->created_at }}</td>
                    <td>{{ $biblioteca->updated_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</form>
@endsection