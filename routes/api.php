<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CidadeController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\PacienteController;

Route::post('login', [AuthController::class, 'login']);
Route::get('user', [AuthController::class, 'me']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('refresh', [AuthController::class, 'refresh']);

Route::group(['prefix' => 'cidades'], function () {
    Route::get('/', [CidadeController::class, 'index']);
    Route::get('{cidade_id}/medicos', [CidadeController::class, 'obterMedicosPorCidade']);
});

Route::group(['prefix' => 'medicos'], function () {
    Route::get('/', [MedicoController::class, 'index']);
    Route::post('/', [MedicoController::class, 'store']);
    Route::get('{medico_id}/pacientes', [MedicoController::class, 'obterConsultas']);
    Route::post('consulta', [MedicoController::class, 'agendarConsulta']);
});

Route::group(['prefix' => 'pacientes'], function () {
    Route::post('/', [PacienteController::class, 'store']);
    Route::put('{id}', [PacienteController::class, 'update']);
});
