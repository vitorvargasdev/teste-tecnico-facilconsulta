<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CidadeService;

class CidadeController extends Controller
{
    private $service;

    public function __construct(CidadeService $service)
    {
        $this->service = $service;
    }

    /**
     * Lista todas as cidades, opcionalmente filtradas por nome
     * 
     * @param Request $request
     * @return App\Models\Cidade Lista de cidades
     */
    public function index(Request $request)
    {
        $nome = $request->input('nome');
        return $this->service->obterCidades($nome);
    }

    /**
     * Lista todos os médicos de uma cidade específica, opcionalmente filtrados por nome
     * 
     * @param int $cidade_id ID da cidade
     * @param Request $request
     * @return App\Models\Medico Lista de médicos
     */
    public function obterMedicosPorCidade($cidade_id, Request $request)
    {
        $nome = $request->input('nome');
        return $this->service->obterMedicosPorCidade($cidade_id, $nome);
    }
}
