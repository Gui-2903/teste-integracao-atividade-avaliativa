<h1>Pessoas</h1>

@foreach ($pessoas as $pessoa)
    <div>{{ $pessoa->name }}</div>
@endforeach
