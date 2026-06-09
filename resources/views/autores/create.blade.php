@extends('layouts.app')

@section('content')

@if (session('error'))
<div class="alert"></div>
    {{ session('error') }}
</div>
@endif

<form action="{{ route('autores.store') }}" method="POST">
    @csrf
    <!-- Form fields for creating autor -->

    <label for="nome">Nome:</label>
    <input type="text" id="nome" name="nome" value="" required>

    <label for="nacionalidade">Nacionalidade:</label>
    <input type="text" id="nacionalidade" name="nacionalidade" value="" required>

    <label for="data_nascimento">Data de Nascimento:</label>
    <input type="date" id="data_nascimento" name="data_nascimento" value="" required>

    <button type="submit">Cadastrar Autor</button>

</form>
@endsection