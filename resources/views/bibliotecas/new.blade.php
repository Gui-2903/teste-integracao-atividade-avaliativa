@extends('layouts.app')

@section('content')

@if (session('error'))
<div class="alert"></div>
    {{ session('error') }}
</div>
@endif

<form action="{{ route('bibliotecas.store') }}" method="POST">
    @csrf
    <!-- Form fields for creating biblioteca -->

    <label for="nome">Nome:</label>
    <input type="text" id="nome" name="nome" value="" required>

    <label for="endereco">Endereço:</label>
    <input type="text" id="endereco" name="endereco" value="" required>

    <label for="telefone">Telefone:</label>
    <input type="text" id="telefone" name="telefone" value="" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="" required>

    <label for="created_by">Responsável:</label>
    <select id="created_by" name="created_by" required>
        @foreach ($users as $user)
            <option value="{{ $user->id }}">{{ $user->name }}</option>
        @endforeach
    </select>

    <button type="submit">Criar Biblioteca</button>

</form>
@endsection