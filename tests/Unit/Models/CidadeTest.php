<?php

namespace Tests\Unit\Models;

use App\Models\Cidade;
use App\Models\Medico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CidadeTest extends TestCase
{
    use RefreshDatabase;

    public function testCidadeCreation()
    {
        $cidade = Cidade::factory()->create([
            'nome' => 'São Paulo',
            'estado' => 'SP',
        ]);

        $this->assertInstanceOf(Cidade::class, $cidade);
        $this->assertEquals('São Paulo', $cidade->nome);
        $this->assertEquals('SP', $cidade->estado);
    }

    public function testCidadeFillableAttributes()
    {
        $fillable = ['id', 'nome', 'estado'];
        $cidade = new Cidade;

        $this->assertEquals($fillable, $cidade->getFillable());
    }

    public function testCidadeHiddenAttributes()
    {
        $hidden = ['created_at', 'updated_at', 'deleted_at'];
        $cidade = new Cidade;

        $this->assertEquals($hidden, $cidade->getHidden());
    }

    public function testCidadeMedicosRelationship()
    {
        $cidade = Cidade::factory()->create();
        Medico::factory()->count(3)->create(['cidade_id' => $cidade->id]);
        $cidade->refresh();

        $this->assertCount(3, $cidade->medicos);
        $this->assertInstanceOf(Medico::class, $cidade->medicos->first());
    }
}