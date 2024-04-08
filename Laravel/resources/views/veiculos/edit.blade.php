<form action="/veiculos/{{ $veiculo->id }}" method="POST">
    @csrf
    @method('PUT') <!-- Sobrescreve o método POST para PUT -->
    <input type="text" name="nome" value="{{ $veiculo->nome }}">
    <input type="text" name="codigo_fipe" value="{{ $veiculo->codigo_fipe }}">
    <input type="text" name="preco" value="{{ $veiculo->preco }}">
    <button type="submit">Atualizar Veículo</button>
</form>
