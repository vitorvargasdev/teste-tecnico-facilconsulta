<?php

namespace Tests\Unit\Models;

use App\Models\Cidade;
use App\Models\Consulta;
use App\Models\Medico;
use App\Models\Paciente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultaTest extends TestCase
{
    use RefreshDatabase;

    public function testConsultaCreation()
    {
        Cidade::factory()->create();
        $medico = Medico::factory()->create();
        $paciente = Paciente::factory()->create();

        $consulta = Consulta::factory()->create([
            'medico_id' => $medico->id,
            'paciente_id' => $paciente->id,
            'data' => '2023-06-15 10:00:00',
        ]);

        $this->assertInstanceOf(Consulta::class, $consulta);
        $this->assertEquals($medico->id, $consulta->medico_id);
        $this->assertEquals($paciente->id, $consulta->paciente_id);
        $this->assertEquals('2023-06-15 10:00:00', $consulta->data);
    }

    public function testConsultaFillableAttributes()
    {
        $fillable = ['id', 'medico_id', 'paciente_id', 'data'];
        $consulta = new Consulta;

        $this->assertEquals($fillable, $consulta->getFillable());
    }

    public function testConsultaHiddenAttributes()
    {
        $hidden = ['medico_id', 'paciente_id', 'created_at', 'updated_at', 'deleted_at'];
        $consulta = new Consulta;

        $this->assertEquals($hidden, $consulta->getHidden());
    }

    public function testConsultaPacienteRelationship()
    {
        Cidade::factory()->create();
        $paciente = Paciente::factory()->create();
        $medico = Medico::factory()->create();
        $consulta = Consulta::factory()->create(['paciente_id' => $paciente->id, 'medico_id' => $medico->id]);

        $this->assertInstanceOf(Paciente::class, $consulta->paciente);
        $this->assertEquals($paciente->id, $consulta->paciente->id);
    }

    public function testConsultaMedicoRelationship()
    {
        Cidade::factory()->create();
        $paciente = Paciente::factory()->create();
        $medico = Medico::factory()->create();
        $consulta = Consulta::factory()->create(['paciente_id' => $paciente->id, 'medico_id' => $medico->id]);

        $this->assertInstanceOf(Medico::class, $consulta->medico);
        $this->assertEquals($medico->id, $consulta->medico->id);
    }
}
