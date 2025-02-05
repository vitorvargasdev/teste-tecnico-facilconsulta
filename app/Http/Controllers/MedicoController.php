<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MedicoService;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

use App\Http\Requests\StoreMedicoRequest;
use App\Http\Requests\StoreConsultaRequest;

class MedicoController extends Controller implements HasMiddleware
{
    private $service;

    public function __construct(MedicoService $service)
    {
        $this->service = $service;
    }

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth:api', except: ['index']),
        ];
    }

    /**
     * Lista todos os médicos, opcionalmente filtrados por nome
     *
     * @param Request $request
     * @return App\Models\Medico Lista de médicos
     */
    public function index(Request $request)
    {
        $nome = $request->input('nome');
        return $this->service->listarMedicos($nome);
    }

    /**
     * Cria um novo médico
     *
     * @param StoreMedicoRequest $request
     * @return \App\Models\Medico Médico criado
     */
    public function store(StoreMedicoRequest $request)
    {
        $data = $request->validated();
        return $this->service->criarMedico($data);
    }

    /**
     * Obtém consultas de um médico específico, com filtros opcionais
     *
     * @param int $medico_id ID do médico
     * @param Request $request
     * @return App\Models\Paciente Lista de pacientes com consultas
     */
    public function obterConsultas($medico_id, Request $request)
    {
        $nome = $request->input('nome');
        $apenas_agendadas = $request->input('apenas-agendadas') === 'true';
        return $this->service->obterConsultas($medico_id, $nome, $apenas_agendadas);
    }

    /**
     * Agenda uma nova consulta
     *
     * @param StoreConsultaRequest $request
     * @return \App\Models\Consulta Consulta agendada
     */
    public function agendarConsulta(StoreConsultaRequest $request) {
        $data = $request->validated();
        return $this->service->agendarConsulta($data);
    }
}
