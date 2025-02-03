<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

use App\Http\Requests\StorePacienteRequest;
use App\Http\Requests\UpdatePacienteRequest;
use App\Services\PacienteService;

class PacienteController extends Controller implements HasMiddleware
{
    private $service;

    public function __construct(PacienteService $service)
    {
        $this->service = $service;
    }

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth:api'),
        ];
    }

    /**
     * Cria um novo paciente
     *
     * @param StorePacienteRequest $request
     * @return \App\Models\Paciente Paciente criado
     */
    public function store(StorePacienteRequest $request)
    {
        $data = $request->validated();
        return $this->service->criarPaciente($data);
    }

    /**
     * Atualiza um paciente existente
     *
     * @param UpdatePacienteRequest $request
     * @param int $id ID do paciente
     * @return \App\Models\Paciente Paciente atualizado
     */
    public function update(UpdatePacienteRequest $request, $id)
    {
        $data = $request->validated();
        return $this->service->atualizarPaciente($id, $data);
    }
}
