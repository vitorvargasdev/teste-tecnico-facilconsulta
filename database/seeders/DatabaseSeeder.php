<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cidade;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Consulta;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'christian.ramires',
            'email' => 'christian.ramires@example.com',
            'password' => Hash::make('password'),
        ]);

        Cidade::factory()->count(15)->create();
        Medico::factory()->count(15)->create();
        Paciente::factory()->count(15)->create();
        Consulta::factory()->count(15)->create();
    }
}
