@extends('layouts.app')

@section('content')

@if (session('message'))
<div class="alert"></div>
    {{ session('message') }}
</div>
@endif

<form action="{{ route('autores.update', ['id' => $autor->id]) }}" method="POST">
    @csrf
    @method('PUT')
    <!-- Form fields for editing autor -->

    <label for="nome">Nome:</label>
    <input type="text" id="nome" name="nome" value="{{ $autor->nome }}" required>

    <label for="nacionalidade">Nacionalidade:</label>
    <input type="text" id="nacionalidade" name="nacionalidade" value="{{ $autor->nacionalidade }}" required>

    <label for="data_nascimento">Data de Nascimento:</label>
    <input type="date" id="data_nascimento" name="data_nascimento" value="{{ $autor->data_nascimento }}" required>

    <button type="submit">Atualizar Autor</button>

</form>
@endsection