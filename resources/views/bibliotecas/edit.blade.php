@extends('layouts.app')

@section('content')

@if (session('message'))
<div class="alert"></div>
    {{ session('message') }}
</div>
@endif

<form action="{{ route('bibliotecas.update', ['id' => $biblioteca->id]) }}" method="POST">
    @csrf
    @method('PUT')
    <!-- Form fields for editing biblioteca -->

    <label for="nome">Nome:</label>
    <input type="text" id="nome" name="nome" value="{{ $biblioteca->nome }}" required>

    <label for="endereco">Endereço:</label>
    <input type="text" id="endereco" name="endereco" value="{{ $biblioteca->endereco }}" required>

    <label for="telefone">Telefone:</label>
    <input type="text" id="telefone" name="telefone" value="{{ $biblioteca->telefone }}" required>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="{{ $biblioteca->email }}" required>

    <button type="submit">Atualizar Biblioteca</button>

</form>



<hr>

<h4>Pessoas</h4>

<table>
    <thead>
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Telefone</th>
            <th>Matrícula</th>
            <th>Bibliotecas</th>
        </tr>
    </thead>
    <tbody>
        @foreach($biblioteca->pessoas as $pessoa)
            <tr>
                <td>{{ $pessoa->name }}</td>
                <td>{{ $pessoa->email }}</td>
                <td>{{ $pessoa->telefone }}</td>
                <td>{{ $pessoa->matricula }}</td>
                <td>{{ $pessoa->bibliotecas->pluck('nome')->join(', ') }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4">Total de Pessoas: {{ count($biblioteca->pessoas) }}</td>
            <td><a href="{{ route('bibliotecas.pessoas.create', ['biblioteca' => $biblioteca->id]) }}" class="btn">Adicionar Pessoa à Biblioteca</a></td>
        </tr>
    </tfoot>
</table>

@endsection