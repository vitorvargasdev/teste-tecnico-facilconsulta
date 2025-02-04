<?php

namespace Tests\Unit;

use App\Models\Cidade;
use App\Models\Paciente;
use App\Models\Medico;
use App\Models\Consulta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PacienteTest extends TestCase
{
    use RefreshDatabase;

    public function testPacienteCreation()
    {
        $paciente = Paciente::factory()->create([
            'nome' => 'John Doe',
            'cpf' => '12345678901',
            'celular' => '11999999999',
        ]);

        $this->assertInstanceOf(Paciente::class, $paciente);
        $this->assertEquals('John Doe', $paciente->nome);
        $this->assertEquals('12345678901', $paciente->cpf);
        $this->assertEquals('11999999999', $paciente->celular);
    }

    public function testPacienteFillableAttributes()
    {
        $fillable = ['id', 'nome', 'cpf', 'celular'];
        $paciente = new Paciente;

        $this->assertEquals($fillable, $paciente->getFillable());
    }

    public function testPacienteHiddenAttributes()
    {
        $hidden = ['created_at', 'updated_at', 'deleted_at'];
        $paciente = new Paciente;

        $this->assertEquals($hidden, $paciente->getHidden());
    }

    public function testPacienteConsultasRelationship()
    {
        Cidade::factory()->create();
        $paciente = Paciente::factory()->create();
        $medico = Medico::factory()->create();
        Consulta::factory()->count(3)->create(['paciente_id' => $paciente->id, 'medico_id' => $medico->id]);
        $paciente->refresh();

        $this->assertCount(3, $paciente->consultas);
        $this->assertInstanceOf(Consulta::class, $paciente->consultas->first());
    }
}
