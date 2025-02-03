<?php

namespace App\Services;

use App\Models\Cidade;
use App\Models\Medico;

class CidadeService
{
    /**
     * Obtém lista de cidades, opcionalmente filtrada por nome
     *
     * @param string|null $nome Nome para filtrar cidades
     * @return App\Models\Cidade Lista de cidades
     */
    public function obterCidades(?string $nome) 
    {
        $query = Cidade::query();

        if ($nome) {
            $query->where('nome', 'like', '%' . $nome . '%');
        }

        return $query->orderBy('nome', 'asc')->get();
    }

    /**
     * Obtém lista de médicos de uma cidade específica, opcionalmente filtrada por nome
     *
     * @param int $cidade_id ID da cidade
     * @param string|null $nome Nome para filtrar médicos
     * @return App\Models\Medico Lista de médicos
     */
    public function obterMedicosPorCidade(int $cidade_id, ?string $nome)
    {
        $query = Medico::query();

        if ($nome) {
            $query->where('nome', 'like', '%' . $nome . '%');
        }

        return $query
            ->where('cidade_id', $cidade_id)
            ->orderBy('nome', 'asc')->get();
    }
}
