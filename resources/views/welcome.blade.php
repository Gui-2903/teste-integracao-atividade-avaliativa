@extends('layouts.app')

@section('content')
<div class="section">
    <div class="row">
        <div class="col s12 m10 l8 offset-m1 offset-l2">
            <div class="card blue-grey darken-1">
                <div class="card-content white-text">
                    <span class="card-title">Bem-vindo à Biblioteca</span>
                    <p>Este app agora usa Material Design via Materialize, sem depender de Node ou build local.</p>
                </div>
                <div class="card-action">
                    <p><a href="{{ route('bibliotecas.index') }}">Ver Bibliotecas</a></p>
                    <p><a href="{{ route('users.index') }}">Ver Usuários</a></p>
                    <p><a href="{{ route('pessoas.index') }}">Ver Pessoas</a></p>
                    <p><a href="{{ route('autores.index') }}">Ver Autores</a></p>
                    <p><a href="{{ route('livros.index') }}">Ver Livros</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
