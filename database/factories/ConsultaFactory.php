<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Medico;
use App\Models\Paciente;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ConsultaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'medico_id' => Medico::inRandomOrder()->first()->id,
            'paciente_id' => Paciente::inRandomOrder()->first()->id,
            'data' => fake()->dateTimeBetween("now", "+3 months"),
        ];
    }
}
