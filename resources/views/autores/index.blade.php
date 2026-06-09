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

<a href="{{ route('autores.create') }}">Cadastrar novo Autor</a>

<table>
    <thead>
        <tr>
            <td>id</td>
            <td>nome</td>
            <td>nacionalidade</td>
            <td>data_nascimento</td>
            <td>created_at</td>
            <td>updated_at</td>
        </tr>
    </thead>
    <tbody>
        @foreach ($autores as $autor)
            <tr>
                <td>{{ $autor->id }}</td>
                <td>
                    <a href="{{ route('autores.edit', ['id' => $autor->id]) }}">{{ $autor->nome }}</a>
                </td>
                <td>{{ $autor->nacionalidade }}</td>
                <td>{{ $autor->data_nascimento }}</td>
                <td>{{ $autor->created_at }}</td>
                <td>{{ $autor->updated_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
