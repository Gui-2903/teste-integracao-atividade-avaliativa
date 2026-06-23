@extends('layouts.app')

@section('content')

@if (session('error'))
<div class="alert"></div>
    {{ session('error') }}
</div>
@endif

<form action="{{ route('livros.store') }}" method="POST">
    @csrf
    <!-- Form fields for creating livro -->

    <label for="titulo">Titulo:</label>
    <input type="text" id="titulo" name="titulo" value="" required>

    <label for="isbn">ISBN:</label>
    <input type="text" id="isbn" name="isbn" value="" required>

    <label for="data_publicacao">Data de Publicação:</label>
    <input type="date" id="data_publicacao" name="data_publicacao" value="" required>

    <label for="autor_id">Autor:</label>
    <select id="autor_id" name="autor_id" required>
        @foreach ($autores as $autor)
            <option value="{{ $autor->id }}">{{ $autor->nome }}</option>
        @endforeach
    </select>

    <button type="submit">Cadastrar Livro</button>

</form>
@endsection