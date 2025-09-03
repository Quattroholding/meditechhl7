<?php

namespace Tests\Feature\Api;

use App\Models\Client;
use App\Models\MedicationRequest;
use App\Models\Medicine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
    }

    public function test_can_create_medicine()
    {
        $client = Client::factory()->create();

        $medicineData = [
            'generic_name' => 'Ibuprofeno',
            'type' => 'Tableta',
            'mgs' => 400,
            'mgs_type' => 'mg',
            'client_id' => $client->id,
            'home_name' => 'Advil',
            'ndc_code' => '12345-6789-01',
            'price' => 25.50,
            'product_type' => 'Antiinflamatorio',
            'active' => true,
            'narcotic' => false,
        ];

        $response = $this->postJson('/api/medicines', $medicineData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'generic_name',
                    'full_name',
                    'type',
                    'mgs',
                    'mgs_type',
                    'concentration',
                    'active',
                    'narcotic',
                    'price',
                    'client',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('medicines', [
            'generic_name' => 'Ibuprofeno',
            'type' => 'Tableta',
            'mgs' => 400,
            'client_id' => $client->id,
            'source' => 'api',
        ]);
    }

    public function test_can_list_medicines_with_client_filter()
    {
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();

        Medicine::factory()->count(3)->create(['client_id' => $client1->id]);
        Medicine::factory()->count(2)->create(['client_id' => $client2->id]);

        $response = $this->getJson("/api/medicines?client_id={$client1->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'generic_name',
                        'full_name',
                        'type',
                        'mgs',
                        'concentration',
                        'active',
                        'narcotic',
                    ],
                ],
                'links',
                'meta',
            ]);

        $this->assertEquals(3, count($response->json('data')));
    }

    public function test_can_list_medicines_by_client_endpoint()
    {
        $client = Client::factory()->create();
        Medicine::factory()->count(3)->create(['client_id' => $client->id, 'active' => true]);
        Medicine::factory()->create(['client_id' => $client->id, 'active' => false]); // Inactive

        $response = $this->getJson("/api/clients/{$client->id}/medicines");

        $response->assertStatus(200);
        $this->assertEquals(3, count($response->json('data'))); // Only active medicines
    }

    public function test_can_show_medicine()
    {
        $medicine = Medicine::factory()->create();

        $response = $this->getJson("/api/medicines/{$medicine->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'generic_name',
                    'full_name',
                    'type',
                    'concentration',
                    'active',
                    'client',
                ],
            ]);
    }

    public function test_can_update_medicine()
    {
        $client = Client::factory()->create();
        $medicine = Medicine::factory()->create(['client_id' => $client->id]);

        $updateData = [
            'generic_name' => 'Paracetamol',
            'type' => 'Capsula',
            'mgs' => 500,
            'mgs_type' => 'mg',
            'client_id' => $client->id,
            'price' => 15.00,
            'active' => false,
        ];

        $response = $this->putJson("/api/medicines/{$medicine->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ]);

        $this->assertDatabaseHas('medicines', [
            'id' => $medicine->id,
            'generic_name' => 'Paracetamol',
            'type' => 'Capsula',
            'mgs' => 500,
            'active' => false,
        ]);
    }

    public function test_can_delete_medicine()
    {
        $medicine = Medicine::factory()->create();

        $response = $this->deleteJson("/api/medicines/{$medicine->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
            ]);

        $this->assertSoftDeleted('medicines', [
            'id' => $medicine->id,
        ]);
    }

    public function test_cannot_delete_medicine_with_medication_requests()
    {
        $medicine = Medicine::factory()->create();
        MedicationRequest::factory()->create(['medication_id' => $medicine->id]);

        $response = $this->deleteJson("/api/medicines/{$medicine->id}");

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Cannot delete medicine. It is being used in medication requests.',
            ]);
    }

    public function test_validation_errors_on_create()
    {
        $response = $this->postJson('/api/medicines', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['generic_name', 'type', 'mgs', 'mgs_type', 'client_id']);
    }

    public function test_unique_ndc_code_validation()
    {
        $client = Client::factory()->create();
        Medicine::factory()->create(['ndc_code' => '12345-6789-01']);

        $medicineData = [
            'generic_name' => 'Test Medicine',
            'type' => 'Tableta',
            'mgs' => 100,
            'mgs_type' => 'mg',
            'client_id' => $client->id,
            'ndc_code' => '12345-6789-01', // Duplicate
        ];

        $response = $this->postJson('/api/medicines', $medicineData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ndc_code']);
    }

    public function test_can_search_medicines()
    {
        $client = Client::factory()->create();
        Medicine::factory()->create([
            'client_id' => $client->id,
            'generic_name' => 'Ibuprofeno',
            'home_name' => 'Advil',
        ]);
        Medicine::factory()->create([
            'client_id' => $client->id,
            'generic_name' => 'Paracetamol',
            'home_name' => 'Tylenol',
        ]);

        $response = $this->getJson("/api/medicines?client_id={$client->id}&search=Ibu");

        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('data')));
    }

    public function test_client_filtering_for_show()
    {
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();
        $medicine = Medicine::factory()->create(['client_id' => $client1->id]);

        $response = $this->getJson("/api/medicines/{$medicine->id}?client_id={$client2->id}");

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Medicine not found or access denied.',
            ]);
    }

    public function test_client_filtering_for_update()
    {
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();
        $medicine = Medicine::factory()->create(['client_id' => $client1->id]);

        $updateData = [
            'generic_name' => 'Updated Medicine',
            'type' => 'Tableta',
            'mgs' => 200,
            'mgs_type' => 'mg',
            'client_id' => $client2->id,
        ];

        $response = $this->putJson("/api/medicines/{$medicine->id}?client_id={$client2->id}", $updateData);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Medicine not found or access denied.',
            ]);
    }

    public function test_can_filter_by_active_status()
    {
        $client = Client::factory()->create();
        Medicine::factory()->count(2)->create(['client_id' => $client->id, 'active' => true]);
        Medicine::factory()->count(3)->create(['client_id' => $client->id, 'active' => false]);

        $response = $this->getJson("/api/medicines?client_id={$client->id}&active=1");

        $response->assertStatus(200);
        $this->assertEquals(2, count($response->json('data')));
    }

    public function test_can_filter_by_narcotic_status()
    {
        $client = Client::factory()->create();
        Medicine::factory()->count(2)->create(['client_id' => $client->id, 'narcotic' => true]);
        Medicine::factory()->count(3)->create(['client_id' => $client->id, 'narcotic' => false]);

        $response = $this->getJson("/api/medicines?client_id={$client->id}&narcotic=1");

        $response->assertStatus(200);
        $this->assertEquals(2, count($response->json('data')));
    }
}
