<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Veiculo; // Adicione esta linha para usar o Model Veiculo

class VeiculoController extends Controller
{
    // Método para mostrar a página inicial com o formulário e a lista de veículos
    public function index()
    {
        $veiculos = Veiculo::all(); // Pega todos os veículos do banco de dados
        return view('veiculos.index', compact('veiculos'));
    }

    // Método para armazenar um novo veículo no banco de dados
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required',
            'codigo_fipe' => 'required',
            'preco' => 'required|numeric'
        ]);

        Veiculo::create($request->all()); // Cria um novo veículo com os dados do formulário

        return redirect('/')->with('success', 'Veículo adicionado com sucesso!');
    }
    public function edit($id)
    {
        $veiculo = Veiculo::findOrFail($id);
        return view('veiculos.edit', compact('veiculo'));
    }
    public function update(Request $request, $id)
    {
        if ($request->has('_method') && $request->input('_method') === 'PUT') {
            $veiculo = Veiculo::findOrFail($id);
    
            $validatedData = $request->validate([
                'nome' => 'required|max:150',
                'codigo_fipe' => 'required|max:15',
                'preco' => 'required|numeric',
            ]);
    
            $veiculo->update($validatedData);
    
            return response()->json(['message' => 'Veículo atualizado com sucesso!']);
        } else {
            return response()->json(['error' => 'Método inválido'], 405);
        }
    }
    

    
    
    public function destroy($id)
{
    $veiculo = Veiculo::findOrFail($id);
    $veiculo->delete();

    // Retorna uma resposta JSON para chamadas AJAX
    return response()->json(['success' => 'Veículo removido com sucesso!']);
}

}
