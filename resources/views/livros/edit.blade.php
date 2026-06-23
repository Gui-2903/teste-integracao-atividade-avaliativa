@extends('layouts.app')

@section('content')

@if (session('error'))
<div class="alert"></div>
    {{ session('error') }}
</div>
@endif

<form action="{{ route('livros.update', ['id' => $livro->id]) }}" method="POST">
    @csrf
    @method('PUT')
    <!-- Form fields for editing livro -->

    <label for="titulo">Titulo:</label>
    <input type="text" id="titulo" name="titulo" value="{{ $livro->titulo }}" required>

    <label for="isbn">ISBN:</label>
    <input type="text" id="isbn" name="isbn" value="{{ $livro->isbn }}" required>

    <label for="data_publicacao">Data de Publicação:</label>
    <input type="date" id="data_publicacao" name="data_publicacao" value="{{ $livro->data_publicacao }}" required>

    <label for="autor_id">Autor:</label>
    <select id="autor_id" name="autor_id" required>
        @foreach ($autores as $autor)
            <option value="{{ $autor->id }}" {{ $livro->autor_id == $autor->id ? 'selected' : '' }}>{{ $autor->nome }}</option>
        @endforeach
    </select>

    <button type="submit">Cadastrar Livro</button>

</form>
@endsection