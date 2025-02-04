<?php

namespace Tests\Feature\Cidade;

use Tests\TestCase;
use App\Models\Cidade;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ObterCidadesTest extends TestCase
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
}
