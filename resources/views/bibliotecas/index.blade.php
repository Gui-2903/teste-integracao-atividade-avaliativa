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

<a href="{{ route('bibliotecas.create') }}">Criar Nova Biblioteca</a>

<table>
    <thead>
        <tr>
            <td>id</td>
            <td>responsavel</td>
            <td>nome</td>
            <td>endereco</td>
            <td>telefone</td>
            <td>email</td>
            <td>created_at</td>
            <td>updated_at</td>
        </tr>
    </thead>
    <tbody>
        @foreach ($bibliotecas as $biblioteca)
            <tr>
                <td>{{ $biblioteca->id }}</td>
                <td>
                    <a href="{{ route('bibliotecas.edit', ['id' => $biblioteca->id]) }}">{{ $biblioteca->nome }}</a>
                </td>
                <td>{{ $biblioteca->creator->name }}</td>
                <td>{{ $biblioteca->endereco }}</td>
                <td>{{ $biblioteca->telefone }}</td>
                <td>{{ $biblioteca->email }}</td>
                <td>{{ $biblioteca->created_at }}</td>
                <td>{{ $biblioteca->updated_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
