<?php

namespace Tests\Unit\Models;

use App\Models\Medico;
use App\Models\Cidade;
use App\Models\Consulta;
use App\Models\Paciente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicoTest extends TestCase
{
    use RefreshDatabase;

    public function testMedicoCreation()
    {
        $cidade = Cidade::factory()->create();
        $medico = Medico::factory()->create([
            'nome' => 'Dr. John Doe',
            'especialidade' => 'Cardiologia',
            'cidade_id' => $cidade->id,
        ]);

        $this->assertInstanceOf(Medico::class, $medico);
        $this->assertEquals('Dr. John Doe', $medico->nome);
        $this->assertEquals('Cardiologia', $medico->especialidade);
        $this->assertEquals($cidade->id, $medico->cidade_id);
    }

    public function testMedicoFillableAttributes()
    {
        $fillable = ['id', 'nome', 'especialidade', 'cidade_id'];
        $medico = new Medico;

        $this->assertEquals($fillable, $medico->getFillable());
    }

    public function testMedicoHiddenAttributes()
    {
        $hidden = ['created_at', 'updated_at', 'deleted_at'];
        $medico = new Medico;

        $this->assertEquals($hidden, $medico->getHidden());
    }

    public function testMedicoCidadeRelationship()
    {
        $cidade = Cidade::factory()->create();
        $medico = Medico::factory()->create(['cidade_id' => $cidade->id]);

        $this->assertInstanceOf(Cidade::class, $medico->cidade);
        $this->assertEquals($cidade->id, $medico->cidade->id);
    }

    public function testMedicoConsultasRelationship()
    {
        Cidade::factory()->create();
        $paciente = Paciente::factory()->create();
        $medico = Medico::factory()->create();
        Consulta::factory()->create(['medico_id' => $medico->id, 'paciente_id' => $paciente->id]);

        $this->assertCount(1, $medico->consultas);
        $this->assertInstanceOf(Consulta::class, $medico->consultas->first());
    }
}