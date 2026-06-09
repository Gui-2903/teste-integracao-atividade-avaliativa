@extends('layouts.app')

@section('content')

@if (session('message'))
<div class="alert">{{ session('message') }}</div>
@endif

@if (session('error'))
<div class="alert">{{ session('error') }}</div>
@endif

<h3>Adicionar Pessoa à Biblioteca: {{ $biblioteca->nome }}</h3>

<p><strong>Endereço:</strong> {{ $biblioteca->endereco }}</p>

@if($pessoas->isEmpty())
    <p>Nenhuma pessoa disponível para associar. Cadastre uma nova pessoa ou remova uma associação antes.</p>
@else
    <form action="{{ route('bibliotecas.pessoas.store', ['biblioteca' => $biblioteca->id]) }}" method="POST">
        @csrf

        <label for="pessoa_id">Pessoa</label>
        <select id="pessoa_id" name="pessoa_id" required>
            <option value="">Selecione uma pessoa</option>
            @foreach($pessoas as $pessoa)
                <option value="{{ $pessoa->id }}">{{ $pessoa->name }} ({{ $pessoa->email }})</option>
            @endforeach
        </select>

        <button type="submit" class='btn btn-warning'>Adicionar Pessoa</button>
    </form>

    <hr>
@endif

<h4>Pessoas já associadas</h4>
@if($biblioteca->pessoas->isEmpty())
    <p>Nenhuma pessoa associada a esta biblioteca ainda.</p>
@else
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>Matrícula</th>
            </tr>
        </thead>
        <tbody>
            @foreach($biblioteca->pessoas as $pessoa)
                <tr>
                    <td>{{ $pessoa->name }}</td>
                    <td>{{ $pessoa->email }}</td>
                    <td>{{ $pessoa->telefone }}</td>
                    <td>{{ $pessoa->matricula }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif


<hr>
<div>
    <a href="{{ route('pessoas.create') }}" class="btn btn-primary">Cadastrar nova Pessoa</a>
</div>


@endsection
