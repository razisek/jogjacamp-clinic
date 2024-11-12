<?php

namespace Tests\Feature;

use App\Jobs\ProcessCheckupQueue;
use App\Models\Appointment;
use App\Models\Diagnose;
use App\Models\Patient;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ClinicAppointmentTest extends TestCase
{
    use RefreshDatabase;

    protected array $patients = [];
    protected array $diagnoses = [];
    protected array $services = [];
    protected array $appointments = [];

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    public function test_01_create_patients(): void
    {
        // Test Budi
        $response = $this->postJson('/api/patient', ['name' => 'Budi']);
        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'data' => ['id', 'name']])
            ->assertJsonPath('data.name', 'Budi');
        $this->patients['budi'] = Patient::find($response->json('data.id'));

        // Test Indah
        $response = $this->postJson('/api/patient', ['name' => 'Indah']);
        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'data' => ['id', 'name']])
            ->assertJsonPath('data.name', 'Indah');
        $this->patients['indah'] = Patient::find($response->json('data.id'));

        // Test Siska
        $response = $this->postJson('/api/patient', ['name' => 'Siska']);
        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'data' => ['id', 'name']])
            ->assertJsonPath('data.name', 'Siska');
        $this->patients['siska'] = Patient::find($response->json('data.id'));

        $this->assertEquals(3, Patient::count(), 'Should have exactly 3 patients in database');
    }

    public function test_02_create_diagnoses(): void
    {
        // Test Ringan
        $response = $this->postJson('/api/diagnose', ['name' => 'Ringan']);
        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'data' => ['id', 'name']])
            ->assertJsonPath('data.name', 'Ringan');
        $this->diagnoses['ringan'] = Diagnose::find($response->json('data.id'));

        // Test Berat
        $response = $this->postJson('/api/diagnose', ['name' => 'Berat']);
        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'data' => ['id', 'name']])
            ->assertJsonPath('data.name', 'Berat');
        $this->diagnoses['berat'] = Diagnose::find($response->json('data.id'));

        // Test Kritis
        $response = $this->postJson('/api/diagnose', ['name' => 'Kritis']);
        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'data' => ['id', 'name']])
            ->assertJsonPath('data.name', 'Kritis');
        $this->diagnoses['kritis'] = Diagnose::find($response->json('data.id'));

        $this->assertEquals(3, Diagnose::count(), 'Should have exactly 3 diagnoses in database');
    }

    public function test_03_create_services(): void
    {
        // Test Obat 
        $response = $this->postJson('/api/service', ['name' => 'Obat']);
        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'data' => ['id', 'name']])
            ->assertJsonPath('data.name', 'Obat');
        $this->services['obat'] = Service::find($response->json('data.id'));

        // Test Rawat Inap 
        $response = $this->postJson('/api/service', ['name' => 'Rawat Inap']);
        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'data' => ['id', 'name']])
            ->assertJsonPath('data.name', 'Rawat Inap');
        $this->services['rawat_inap'] = Service::find($response->json('data.id'));

        // Test ICU 
        $response = $this->postJson('/api/service', ['name' => 'ICU']);
        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'data' => ['id', 'name']])
            ->assertJsonPath('data.name', 'ICU');
        $this->services['icu'] = Service::find($response->json('data.id'));

        $this->assertEquals(3, Service::count(), 'Should have exactly 3 services in database');
    }

    public function test_04_create_appointments(): void
    {
        $this->test_01_create_patients();
        $this->test_02_create_diagnoses();
        $this->test_03_create_services();

        // Test appointment for Budi (Ringan)
        $response = $this->postJson('/api/appointment', [
            'patient_id' => $this->patients['budi']->id,
            'diagnose_id' => $this->diagnoses['ringan']->id,
        ]);
        $response->assertStatus(201);
        $this->appointments['budi'] = Appointment::find($response->json('data.id'));

        // Test appointment for Indah (Berat)
        $response = $this->postJson('/api/appointment', [
            'patient_id' => $this->patients['indah']->id,
            'diagnose_id' => $this->diagnoses['berat']->id,
        ]);
        $response->assertStatus(201);
        $this->appointments['indah'] = Appointment::find($response->json('data.id'));

        // Test appointment for Siska (Kritis)
        $response = $this->postJson('/api/appointment', [
            'patient_id' => $this->patients['siska']->id,
            'diagnose_id' => $this->diagnoses['kritis']->id,
        ]);
        $response->assertStatus(201);
        $this->appointments['siska'] = Appointment::find($response->json('data.id'));

        $this->assertEquals(3, Appointment::count(), 'Should have exactly 3 appointments in database');

        Queue::assertPushed(ProcessCheckupQueue::class, 3);
    }

    public function test_05_process_checkup_queues(): void
    {
        $this->test_04_create_appointments();

        foreach ($this->appointments as $appointment) {
            (new ProcessCheckupQueue($appointment))->handle();
        }

        // Verify Budi appointment (Ringan)
        $budiCheckups = $this->appointments['budi']->checkupProgress;
        $this->assertEquals(1, $budiCheckups->count(), 'Budi should have exactly 1 service (Obat)');
        $this->assertEquals('Obat', $budiCheckups->first()->service->name);

        // Verify Indah appointment (Berat)
        $indahCheckups = $this->appointments['indah']->checkupProgress;
        $this->assertEquals(2, $indahCheckups->count(), 'Indah should have exactly 2 services (Obat, Rawat Inap)');
        $this->assertTrue($indahCheckups->pluck('service.name')->contains('Obat'));
        $this->assertTrue($indahCheckups->pluck('service.name')->contains('Rawat Inap'));

        // Verify Siska appointment (Kritis)
        $siskaCheckups = $this->appointments['siska']->checkupProgress;
        $this->assertEquals(3, $siskaCheckups->count(), 'Siska should have exactly 3 services (Obat, Rawat Inap, ICU)');
        $this->assertTrue($siskaCheckups->pluck('service.name')->contains('Obat'));
        $this->assertTrue($siskaCheckups->pluck('service.name')->contains('Rawat Inap'));
        $this->assertTrue($siskaCheckups->pluck('service.name')->contains('ICU'));
    }

    public function test_06_update_checkup_progress(): void
    {
        $this->test_05_process_checkup_queues();

        // Update Budicheckup (Ringan - complete)
        $budiCheckup = $this->appointments['budi']->checkupProgress()->first();
        $budiCheckup->update(['status' => 1]);
        $this->assertEquals(1, $budiCheckup->fresh()->status);

        // Update Indah checkups (Berat - complete, ongoing)
        $indahCheckups = $this->appointments['indah']->checkupProgress;
        $indahCheckups->where('service.name', 'Obat')->first()->update(['status' => 1]);
        $indahCheckups->where('service.name', 'Rawat Inap')->first()->update(['status' => 0]);

        $this->assertEquals(
            1,
            $indahCheckups->fresh()->where('service.name', 'Obat')->first()->status
        );
        $this->assertEquals(
            0,
            $indahCheckups->fresh()->where('service.name', 'Rawat Inap')->first()->status
        );

        $siskaCheckups = $this->appointments['siska']->checkupProgress;
        foreach ($siskaCheckups as $checkup) {
            $checkup->update(['status' => true]);
        }
    }

    public function test_07_update_appointment_status(): void
    {
        $this->test_06_update_checkup_progress();

        // Update Budi appointment status (should be complete)
        $response = $this->patchJson("/api/appointment/{$this->appointments['budi']->id}", ['status' => true]);
        $response->assertStatus(200)
            ->assertJson(['data' => ['status' => true]]);

        // Verify Indah appointment status (should remain incomplete)
        $response = $this->getJson("/api/appointment/{$this->appointments['indah']->id}");
        $response->assertStatus(200)
            ->assertJson(['status' => false]);

        // Update Siska appointment status (should be complete)
        $response = $this->patchJson("/api/appointment/{$this->appointments['siska']->id}", ['status' => true]);
        $response->assertStatus(200)
            ->assertJson(['data' => ['status' => true]]);

        $this->assertEquals(
            1,
            $this->appointments['budi']->fresh()->status
        );

        $this->assertEquals(
            0,
            $this->appointments['indah']->fresh()->status
        );

        $this->assertEquals(
            1,
            $this->appointments['siska']->fresh()->status
        );
    }

    public function test_08_verify_final_state(): void
    {
        $this->test_07_update_appointment_status();

        // Verify final for Budi (Ringan)
        $response = $this->getJson("/api/appointment/{$this->appointments['budi']->id}");
        $response->assertStatus(200)
            ->assertJson([
                'patient' => ['name' => 'Budi'],
                'diagnose' => ['name' => 'Ringan'],
                'status' => true,
                'checkup' => [
                    ['service' => ['name' => 'Obat'], 'status' => true]
                ]
            ]);

        // Verify final for Indah (Berat)
        $response = $this->getJson("/api/appointment/{$this->appointments['indah']->id}");
        $response->assertStatus(200)
            ->assertJson([
                'patient' => ['name' => 'Indah'],
                'diagnose' => ['name' => 'Berat'],
                'status' => false,
                'checkup' => [
                    ['service' => ['name' => 'Obat'], 'status' => true],
                    ['service' => ['name' => 'Rawat Inap'], 'status' => false]
                ]
            ]);

        // Verify final for Siska (Kritis)
        $response = $this->getJson("/api/appointment/{$this->appointments['siska']->id}");
        $response->assertStatus(200)
            ->assertJson([
                'patient' => ['name' => 'Siska'],
                'diagnose' => ['name' => 'Kritis'],
                'status' => true,
                'checkup' => [
                    ['service' => ['name' => 'Obat'], 'status' => true],
                    ['service' => ['name' => 'Rawat Inap'], 'status' => true],
                    ['service' => ['name' => 'ICU'], 'status' => true]
                ]
            ]);
    }
}
