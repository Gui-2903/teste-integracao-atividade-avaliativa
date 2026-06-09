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

<a href="{{ route('pessoas.create') }}">Criar Nova Pessoa</a>


<table>
    <thead>
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Telefone</th>
            <th>Matricula</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pessoas as $pessoa)
        <tr>
            <td>
                <a href='{{ route("pessoas.edit", $pessoa->id) }}'>{{ $pessoa->name }}</a>
            </td>
            <td>{{ $pessoa->email }}</td>
            <td>{{ $pessoa->telefone }}</td>
            <td>{{ $pessoa->matricula }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection