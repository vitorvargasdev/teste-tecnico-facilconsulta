<?php

namespace App\Services;

use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Consulta;

class MedicoService
{
    /**
     * Lista todos os médicos, opcionalmente filtrados por nome
     *
     * @param string|null $nome Nome para filtrar médicos
     * @return App\Models\Medico Lista de médicos
     */
    public function listarMedicos(?string $nome)
    {
        $query = Medico::query();

        if ($nome) {
            $query->where('nome', 'like', '%' . $nome . '%');
        }

        return $query->orderBy('nome', 'asc')->get();
    }

    /**
     * Cria um novo médico
     *
     * @param array $data Dados do médico
     * @return \App\Models\Medico Médico criado
     */
    public function criarMedico(array $data)
    {
        return Medico::create($data);
    }

    /**
     * Obtém consultas de um médico específico, com filtros opcionais
     *
     * @param int $medico_id ID do médico
     * @param string|null $nome Nome do paciente para filtrar
     * @param bool|null $apenas_agendadas Filtrar apenas consultas futuras
     * @return App\Models\Paciente Lista de pacientes com consultas
     */
    public function obterConsultas(int $medico_id, ?string $nome, ?bool $apenas_agendadas)
    {
        $pacientes = Paciente::with('consultas')
            ->whereRelation('consultas', 'medico_id', $medico_id);

        if ($apenas_agendadas) {
            $pacientes->whereRelation('consultas', 'data', '>=', now());
        }

        if ($nome) {
            $pacientes->where('nome', 'like', '%' . $nome . '%');
        }

        return $pacientes
            ->orderBy('nome', 'asc')
            ->get();
    }

    /**
     * Agenda uma nova consulta
     *
     * @param array $data Dados da consulta
     * @return \App\Models\Consulta Consulta agendada
     */
    public function agendarConsulta(array $data)
    {
        return Consulta::create($data);
    }
}
