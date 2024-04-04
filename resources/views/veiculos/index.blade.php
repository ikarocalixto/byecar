<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" type="text/css" href="/css/app.css">
  

    <title>Lista de Veículos</title>
    <style>
   
    </style>
</head>
<body>
    <div class="container">
        <h1>Adicionar Veículo</h1>
        
        <form action="/veiculos" method="POST">
            @csrf <!-- Proteção contra CSRF -->
            <label for="nome">Nome do Veículo:</label><br>
            <input type="text" id="nome" name="nome"><br>
            <label for="codigo_fipe">Código FIPE:</label><br>
            <input type="text" id="codigo_fipe" name="codigo_fipe"><br>
            <label for="preco">Preço:</label><br>
            <input type="text" id="preco" name="preco"><br><br>
            <input type="submit" value="Adicionar Veículo">
        </form>

        
        <!-- Campo de Filtro com Botão -->
        <div>
            <h1>Busque o modelo do carro desejado.</h1>
            <input type="text" id="filtroVeiculo" placeholder="Digite o nome do veículo">
            <button onclick="filtrarVeiculos()">Buscar</button>
        </div>
        <h2>Lista de Veículos</h2>
        <div id="containerVeiculos">
            @foreach ($veiculos as $veiculo)
            <div class="veiculo" id="veiculo_{{ $veiculo->id }}">
                Nome: <span id="nome_{{ $veiculo->id }}">{{ $veiculo->nome }}</span><br>
                Código FIPE: <span id="codigo_fipe_{{ $veiculo->id }}">{{ $veiculo->codigo_fipe }}</span><br>
                Preço: <span id="preco_{{ $veiculo->id }}">R$ {{ number_format($veiculo->preco, 2, ',', '.') }}</span><br>
                <button onclick="editarVeiculo('{{ $veiculo->id }}')">Editar</button>
                <button onclick="removerVeiculo('{{ $veiculo->id }}')">Remover</button>
            </div>
            @endforeach
        </div>
    </div>

    <script >


function filtrarVeiculos() {
    var input, filtro, container, veiculos, nome, i, txtValue;
    input = document.getElementById("filtroVeiculo");
    filtro = input.value.toUpperCase();
    container = document.getElementById("containerVeiculos");
    veiculos = container.getElementsByClassName("veiculo");

    for (i = 0; i < veiculos.length; i++) {
        nome = veiculos[i].getElementsByTagName("span")[0];
        if (nome) {
            txtValue = nome.textContent || nome.innerText;
            if (txtValue.toUpperCase().indexOf(filtro) > -1) {
                veiculos[i].style.display = "";
            } else {
                veiculos[i].style.display = "none";
            }
        }
    }
}


function editarVeiculo(id) {
    const nomeEl = document.getElementById('nome_' + id);
    const codigoFipeEl = document.getElementById('codigo_fipe_' + id);
    const precoEl = document.getElementById('preco_' + id);

    const nome = nomeEl.innerText;
    const codigoFipe = codigoFipeEl.innerText;
    const preco = precoEl.innerText;

    nomeEl.innerHTML = `<input type="text" id="input_nome_${id}" value="${nome}">`;
    codigoFipeEl.innerHTML = `<input type="text" id="input_codigo_fipe_${id}" value="${codigoFipe}">`;
    precoEl.innerHTML = `<input type="text" id="input_preco_${id}" value="${preco}">`;

    // Substitui o botão "Editar" por "Salvar"
    const divEl = document.getElementById('veiculo_' + id);
    divEl.innerHTML += `<button onclick="salvarEdicao('${id}')">Salvar</button>`;
}

function salvarEdicao(id) {
    const nome = document.getElementById('input_nome_' + id).value;
    const codigoFipe = document.getElementById('input_codigo_fipe_' + id).value;
    const preco = document.getElementById('input_preco_' + id).value;

    fetch('/veiculos/' + id, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), // Melhor prática para obter o token CSRF
        },
        body: JSON.stringify({
            _method: 'PUT', // Sobrescreve para PUT, indicando que é uma atualização
            nome: nome,
            codigo_fipe: codigoFipe,
            preco: preco
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erro na requisição');
        }
        return response.json();
    })
    .then(data => {
        alert('Veículo atualizado com sucesso!');
        // Opcional: recarregar a página para ver os dados atualizados
        // location.reload();
    })
    .catch(error => console.error('Erro ao atualizar veículo:', error));
}

function removerVeiculo(id) {
    if (!confirm('Tem certeza que deseja remover este veículo?')) {
        return; // Usuário optou por não deletar o veículo
    }

    fetch('/veiculos/' + id, {
        method: 'DELETE', // Utiliza o método DELETE diretamente
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), // CSRF token para segurança
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erro na requisição');
        }
        return response.json();
    })
    .then(data => {
        alert(data.success); // Mostra a mensagem de sucesso
        // Opcional: remover o elemento do DOM ou recarregar a página para refletir a mudança
        document.getElementById('veiculo_' + id).remove();
        // ou
        // location.reload();
    })
    .catch(error => console.error('Erro ao remover veículo:', error));
}
    </script>






</body>
</html>
