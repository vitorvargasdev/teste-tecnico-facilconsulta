<?php

namespace App\Services;

use App\Models\Paciente;

class PacienteService
{
    /**
     * Cria um novo paciente
     *
     * @param array $data Dados do paciente
     * @return \App\Models\Paciente Paciente criado
     */
    public function criarPaciente(array $data)
    {
        return Paciente::create($data);
    }

    /**
     * Atualiza um paciente existente
     *
     * @param int $id ID do paciente
     * @param array $data Dados do paciente
     * @return \App\Models\Paciente Paciente atualizado
     */
    public function atualizarPaciente(int $id, array $data)
    {
        $paciente = Paciente::findOrFail($id);
        $paciente->update($data);
        return $paciente;
    }
}
