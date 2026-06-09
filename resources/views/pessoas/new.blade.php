@extends('layouts.app')

@section('content')

@if (session('error'))
<div class="alert"></div>
    {{ session('error') }}
</div>
@endif

<form action="{{ route('pessoas.store') }}" method="POST">
    @csrf
    @method('POST')
    <!-- Form fields for creating pessoa -->

    <label for="name">Nome:</label>
    <input type="text" id="name" name="name" value="" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="" required>

    <label for="telefone">Telefone:</label>
    <input type="text" id="telefone" name="telefone" value="" required>

    <label for="matricula">Matrícula:</label>
    <input type="text" id="matricula" name="matricula" value="" required>

    <label for="password">Senha:</label>
    <input type="password" id="password" name="password" value="" required>

    <label for="confirmPassword">Confirmar Senha:</label>
    <input type="password" id="confirmPassword" name="confirmPassword" value="" required>

    <button type="submit">Criar Pessoa</button>

</form>


@endsection