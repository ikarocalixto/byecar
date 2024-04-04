<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VeiculoController;

// Rota para a página inicial e listagem de veículos
Route::get('/', [VeiculoController::class, 'index']);
Route::get('/veiculos', [VeiculoController::class, 'index']);

// Rota para tratar o formulário de criação de veículos
Route::post('/veiculos', [VeiculoController::class, 'store']);

Route::post('/veiculos/{id}', [VeiculoController::class, 'update']);

// Rota para atualizar um veículo (PUT)
Route::put('/veiculos/{id}', [VeiculoController::class, 'update']);

// Rota para deletar um veículo (DELETE)
Route::delete('/veiculos/{id}', [VeiculoController::class, 'destroy']);



