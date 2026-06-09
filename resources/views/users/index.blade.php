<h1>Usuários</h1>

@foreach ($users as $user)
    <div>{{ $user->name }}</div>
    <div>{{ $user->email }}</div>
@endforeach
