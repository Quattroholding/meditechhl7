<?php

namespace Tests\Feature\Api;

use App\Models\MedicationRequest;
use App\Models\Patient;
use App\Models\Practitioner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicationRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
    }

    public function test_can_create_medication_request()
    {
        $patient = Patient::factory()->create();
        $practitioner = Practitioner::factory()->create();

        $medicationData = [
            'patient_id' => $patient->id,
            'practitioner_id' => $practitioner->id,
            'medication' => 'Ibuprofeno 400mg',
            'status' => 'active',
            'intent' => 'order',
            'priority' => 'routine',
            'reason' => 'Dolor de cabeza',
            'dosage_text' => '1 tableta cada 8 horas',
            'route' => 'oral',
            'frequency' => 'cada 8 horas',
            'quantity' => 30,
            'refills' => 2,
            'client_id' => $patient->client_id,
            'branch_id' => $patient->branch_id,
        ];

        $response = $this->postJson('/api/medication-requests', $medicationData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'status',
                    'intent',
                    'medication_name',
                    'dosage',
                    'quantity',
                    'refills',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('medication_requests', [
            'patient_id' => $patient->id,
            'practitioner_id' => $practitioner->id,
            'medication' => 'Ibuprofeno 400mg',
            'status' => 'active',
        ]);
    }

    public function test_can_list_medication_requests()
    {
        $medicationRequests = MedicationRequest::factory()->count(3)->create();

        $response = $this->getJson('/api/medication-requests');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'status',
                        'intent',
                        'medication_name',
                        'dosage',
                        'quantity',
                        'refills',
                    ],
                ],
                'links',
                'meta',
            ]);
    }

    public function test_can_show_medication_request()
    {
        $medicationRequest = MedicationRequest::factory()->create();

        $response = $this->getJson("/api/medication-requests/{$medicationRequest->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'status',
                    'intent',
                    'medication_name',
                    'dosage',
                    'quantity',
                    'refills',
                ],
            ]);
    }

    public function test_can_update_medication_request()
    {
        $medicationRequest = MedicationRequest::factory()->create();
        $patient = Patient::factory()->create();
        $practitioner = Practitioner::factory()->create();

        $updateData = [
            'patient_id' => $patient->id,
            'practitioner_id' => $practitioner->id,
            'medication' => 'Paracetamol 500mg',
            'status' => 'completed',
            'intent' => 'order',
            'dosage_text' => '1 tableta cada 6 horas',
            'client_id' => $patient->client_id,
            'branch_id' => $patient->branch_id,
        ];

        $response = $this->putJson("/api/medication-requests/{$medicationRequest->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ]);

        $this->assertDatabaseHas('medication_requests', [
            'id' => $medicationRequest->id,
            'medication' => 'Paracetamol 500mg',
            'status' => 'completed',
        ]);
    }

    public function test_can_delete_medication_request()
    {
        $medicationRequest = MedicationRequest::factory()->create();

        $response = $this->deleteJson("/api/medication-requests/{$medicationRequest->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
            ]);

        $this->assertSoftDeleted('medication_requests', [
            'id' => $medicationRequest->id,
        ]);
    }

    public function test_validation_errors_on_create()
    {
        $response = $this->postJson('/api/medication-requests', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['patient_id', 'practitioner_id', 'status', 'intent', 'client_id', 'branch_id']);
    }

    public function test_can_filter_by_patient_id()
    {
        $patient = Patient::factory()->create();
        MedicationRequest::factory()->count(2)->create(['patient_id' => $patient->id]);
        MedicationRequest::factory()->count(3)->create(); // Different patients

        $response = $this->getJson("/api/medication-requests?patient_id={$patient->id}");

        $response->assertStatus(200);
        $this->assertEquals(2, count($response->json('data')));
    }

    public function test_can_filter_by_practitioner_id()
    {
        $practitioner = Practitioner::factory()->create();
        MedicationRequest::factory()->count(2)->create(['practitioner_id' => $practitioner->id]);
        MedicationRequest::factory()->count(3)->create(); // Different practitioners

        $response = $this->getJson("/api/medication-requests?practitioner_id={$practitioner->id}");

        $response->assertStatus(200);
        $this->assertEquals(2, count($response->json('data')));
    }

    public function test_can_filter_by_status()
    {
        MedicationRequest::factory()->count(2)->create(['status' => 'active']);
        MedicationRequest::factory()->count(3)->create(['status' => 'completed']);

        $response = $this->getJson('/api/medication-requests?status=active');

        $response->assertStatus(200);
        $this->assertEquals(2, count($response->json('data')));
    }
}
