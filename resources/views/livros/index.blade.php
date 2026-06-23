@extends('layouts.app')

@section('content')
@if (session('message'))
<div class="alert"></div>
    {{ session('message') }}
</div>
@endif

@if (session('error'))
<div class="alert"></div>
    {{ session('error') }}
</div>
@endif

<a href="{{ route('livros.create') }}">Cadastrar Novo Livro</a>

<table>
    <thead>
        <tr>
            <td>id</td>
            <td>titulo</td>
            <td>autor</td>
            <td>isbn</td>
            <td>data_publicacao</td>
            <td>created_at</td>
            <td>updated_at</td>
        </tr>
    </thead>
    <tbody>
        @foreach ($livros as $livro)
            <tr>
                <td>{{ $livro->id }}</td>
                <td>
                    <a href="{{ route('livros.edit', ['id' => $livro->id]) }}">{{ $livro->titulo }}</a>
                </td>
                <td>{{ $livro->autor->nome }}</td>
                <td>{{ $livro->isbn }}</td>
                <td>{{ $livro->data_publicacao }}</td>
                <td>{{ $livro->created_at }}</td>
                <td>{{ $livro->updated_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
