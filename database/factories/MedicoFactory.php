<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Provider\pt_BR\Person;
use App\Models\Cidade;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medico>
 */
class MedicoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $person = new Person(fake());
        return [
            'nome' => Person::randomElement([
                "Dr. " . $person->firstNameMale() . " " . $person->lastName(),
                "Dra. " . $person->firstNameFemale() . " " . $person->lastName(),
                "Sr. " . $person->firstNameMale() . " " . $person->lastName(),
                "Srta. " . $person->firstNameFemale() . " " . $person->lastName(),
                $person->firstNameMale() . " " . $person->lastName(),
                $person->firstNameFemale() . " " . $person->lastName(),
            ]),
            'especialidade' => Person::randomElement(['Dermatologia', 'Neurologia', 'Oftalmologia']),
            'cidade_id' => Cidade::inRandomOrder()->first()->id,
        ];
    }
}
