<?php

namespace Tests\Unit\Services;

use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Consulta;
use App\Models\Cidade;
use App\Services\MedicoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicoServiceTest extends TestCase
{
    use RefreshDatabase;

    private MedicoService $medicoService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->medicoService = new MedicoService();
    }

    public function testShouldListDoctors()
    {
        $cidade = Cidade::factory()->create();
        Medico::factory()->count(3)->create(['cidade_id' => $cidade->id]);

        $doctors = $this->medicoService->listarMedicos(null);

        $this->assertCount(3, $doctors->toArray());
        $this->assertInstanceOf(Medico::class, $doctors->first());
    }

    public function testShouldListDoctorsWithNameFilter()
    {
        Cidade::factory()->create();
        Medico::factory()->create(['nome' => 'Dr. João']);
        Medico::factory()->create(['nome' => 'Dr. Maria']);
        Medico::factory()->create(['nome' => 'Dr. Pedro']);

        $doctors = $this->medicoService->listarMedicos('João');

        $this->assertCount(1, $doctors->toArray());
        $this->assertEquals('Dr. João', $doctors->first()->nome);
    }

    public function testShouldCreateDoctor()
    {
        $cidade = Cidade::factory()->create();

        $data = [
            'nome' => 'Dr. Test',
            'especialidade' => 'Cardiologia',
            'cidade_id' => $cidade->id,
        ];

        $doctor = $this->medicoService->criarMedico($data);

        $this->assertInstanceOf(Medico::class, $doctor);
        $this->assertEquals($data['nome'], $doctor->nome);
        $this->assertEquals($data['especialidade'], $doctor->especialidade);
        $this->assertEquals($data['cidade_id'], $doctor->cidade_id);
    }

    public function testShouldGetConsultations()
    {
        $cidade = Cidade::factory()->create();
        $doctor = Medico::factory()->create(['cidade_id' => $cidade->id]);
        $patient1 = Paciente::factory()->create();
        $patient2 = Paciente::factory()->create();

        Consulta::factory()->create([
            'medico_id' => $doctor->id,
            'paciente_id' => $patient1->id,
            'data' => now()->addDay(),
        ]);
        Consulta::factory()->create([
            'medico_id' => $doctor->id,
            'paciente_id' => $patient2->id,
            'data' => now()->subDay(),
        ]);

        $consultations = $this->medicoService->obterConsultas($doctor->id, null, false);

        $this->assertCount(2, $consultations->toArray());
    }

    public function testShouldGetConsultationsWithPatientNameFilter()
    {
        $cidade = Cidade::factory()->create();
        $doctor = Medico::factory()->create(['cidade_id' => $cidade->id]);
        $patient1 = Paciente::factory()->create(['nome' => 'João']);
        $patient2 = Paciente::factory()->create(['nome' => 'Maria']);

        Consulta::factory()->create([
            'medico_id' => $doctor->id,
            'paciente_id' => $patient1->id,
        ]);
        Consulta::factory()->create([
            'medico_id' => $doctor->id,
            'paciente_id' => $patient2->id,
        ]);

        $consultations = $this->medicoService->obterConsultas($doctor->id, 'João', false);

        $this->assertCount(1, $consultations->toArray());
        $this->assertEquals('João', $consultations->first()->nome);
    }

    public function testShouldGetOnlyScheduledConsultations()
    {
        $cidade = Cidade::factory()->create();
        $doctor = Medico::factory()->create(['cidade_id' => $cidade->id]);
        $patient = Paciente::factory()->create();

        Consulta::factory()->create([
            'medico_id' => $doctor->id,
            'paciente_id' => $patient->id,
            'data' => now()->addDays(5),
        ]);
        Consulta::factory()->create([
            'medico_id' => $doctor->id,
            'paciente_id' => $patient->id,
            'data' => now()->subDay(),
        ]);

        $consultations = $this->medicoService->obterConsultas($doctor->id, null, true);
        $date = new \DateTime($consultations->first()->data);
        $now = new \DateTime();
        $isOnlyScheduled = false;

        if ($now >= $date) {
            $isOnlyScheduled = true;
        }

        $this->assertCount(1, $consultations->toArray());
        $this->assertTrue($isOnlyScheduled);
    }

    public function testShouldScheduleConsultation()
    {
        $cidade = Cidade::factory()->create();
        $doctor = Medico::factory()->create(['cidade_id' => $cidade->id]);
        $patient = Paciente::factory()->create();

        $data = [
            'medico_id' => $doctor->id,
            'paciente_id' => $patient->id,
            'data' => now()->addDay()->format('Y-m-d H:i:s'),
        ];

        $consultation = $this->medicoService->agendarConsulta($data);

        $this->assertInstanceOf(Consulta::class, $consultation);
        $this->assertEquals($data['medico_id'], $consultation->medico_id);
        $this->assertEquals($data['paciente_id'], $consultation->paciente_id);
        $this->assertEquals($data['data'], $consultation->data);
    }
}
