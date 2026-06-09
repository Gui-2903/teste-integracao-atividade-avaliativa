<h1>Associar Pessoa</h1>

<select name="pessoa_id">
@foreach ($pessoas as $pessoa)
    <option value="{{ $pessoa->id }}">{{ $pessoa->name }}</option>
@endforeach
</select>
