<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Provider\pt_BR\Person;
use Faker\Provider\pt_BR\PhoneNumber;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PacienteFactory extends Factory
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
                $person->firstNameMale() . " " . $person->lastName(),
                $person->firstNameFemale() . " " . $person->lastName(),
            ]),
            'cpf' => $person->cpf(),
            'celular' => PhoneNumber::cellphoneNumber()
        ];
    }
}
