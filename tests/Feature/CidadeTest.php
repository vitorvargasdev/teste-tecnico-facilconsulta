<?php

namespace Tests\Feature\Cidade;

use Tests\TestCase;
use App\Models\Cidade;
use App\Models\Medico;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CidadeTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_list_all_cities()
    {
        Cidade::factory()->count(3)->create();

        $response = $this->getJson('/api/cidades');

        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => ['id', 'nome']
            ]);
    }

    public function test_should_filter_cities_by_name()
    {
        Cidade::factory()->create(['nome' => 'New York']);
        Cidade::factory()->create(['nome' => 'Los Angeles']);
        Cidade::factory()->create(['nome' => 'Chicago']);

        $response = $this->getJson('/api/cidades?nome=New');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['nome' => 'New York']);
    }

    public function test_should_list_doctors_by_city()
    {
        $city = Cidade::factory()->create();
        Medico::factory()->count(3)->create([
            'cidade_id' => $city->id
        ]);

        $response = $this->getJson("/api/cidades/{$city->id}/medicos");

        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => ['id', 'nome', 'especialidade']
            ]);
    }

    public function test_should_filter_doctors_by_name_in_specific_city()
    {
        $city = Cidade::factory()->create();
        Medico::factory()->create([
            'cidade_id' => $city->id,
            'nome' => 'Dr. John Smith'
        ]);
        Medico::factory()->create([
            'cidade_id' => $city->id,
            'nome' => 'Dr. Jane Doe'
        ]);

        $response = $this->getJson("/api/cidades/{$city->id}/medicos?nome=John");

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['nome' => 'Dr. John Smith']);
    }

    public function test_should_return_empty_list_when_city_has_no_doctors()
    {
        $city = Cidade::factory()->create();

        $response = $this->getJson("/api/cidades/{$city->id}/medicos");

        $response->assertStatus(200)
            ->assertJsonCount(0);
    }

    public function test_should_return_200_when_city_not_found()
    {
        $response = $this->getJson("/api/cidades/999/medicos");

        $response->assertStatus(200);
        $response->assertJsonCount(0);
        $response->assertJsonStructure([]);
    }
}
