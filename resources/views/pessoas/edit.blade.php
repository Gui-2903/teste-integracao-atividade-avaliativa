@extends('layouts.app')

@section('content')

@if (session('error'))
<div class="alert"></div>
    {{ session('error') }}
</div>
@endif

<form action="{{ route('pessoas.update', $pessoa->id) }}" method="POST">
    @csrf
    @method('PUT')
    <!-- Form fields for creating pessoa -->

    <label for="name">Nome:</label>
    <input type="text" id="name" name="name" value="{{ $pessoa->name }}" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="{{ $pessoa->email }}" required>

    <label for="telefone">Telefone:</label>
    <input type="text" id="telefone" name="telefone" value="{{ $pessoa->telefone }}" required>

    <label for="matricula">Matrícula:</label>
    <input type="text" id="matricula" name="matricula" value="{{ $pessoa->matricula }}" required>

    <label for="password">Nova Senha:</label>
    <input type="password" id="password" name="password" value="" >

    <label for="confirmPassword">Confirmar Nova Senha:</label>
    <input type="password" id="confirmPassword" name="confirmPassword" value="" >

    <button type="submit">Atualizar Pessoa</button>

</form>

@endsection