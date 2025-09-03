<?php

namespace Tests\Feature\Api;

use App\Models\Branch;
use App\Models\Client;
use App\Models\ConsultingRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
    }

    public function test_can_create_branch()
    {
        $client = Client::factory()->create();

        $branchData = [
            'client_id' => $client->id,
            'name' => 'Sucursal Norte',
            'phone' => '555-1234',
            'address' => 'Av. Principal 123',
            'type' => 'Clínica',
            'active' => true,
        ];

        $response = $this->postJson('/api/branches', $branchData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'phone',
                    'address',
                    'type',
                    'active',
                    'client',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('branches', [
            'client_id' => $client->id,
            'name' => 'Sucursal Norte',
            'phone' => '555-1234',
            'active' => true,
        ]);
    }

    public function test_can_list_branches_with_client_filter()
    {
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();

        Branch::factory()->count(3)->create(['client_id' => $client1->id]);
        Branch::factory()->count(2)->create(['client_id' => $client2->id]);

        $response = $this->getJson("/api/branches?client_id={$client1->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'phone',
                        'address',
                        'type',
                        'active',
                        'client_name',
                    ],
                ],
                'links',
                'meta',
            ]);

        $this->assertEquals(3, count($response->json('data')));
    }

    public function test_can_list_branches_by_client_endpoint()
    {
        $client = Client::factory()->create();
        Branch::factory()->count(3)->create(['client_id' => $client->id, 'active' => true]);
        Branch::factory()->create(['client_id' => $client->id, 'active' => false]); // Inactive

        $response = $this->getJson("/api/clients/{$client->id}/branches");

        $response->assertStatus(200);
        $this->assertEquals(3, count($response->json('data'))); // Only active branches
    }

    public function test_can_show_branch()
    {
        $branch = Branch::factory()->create();

        $response = $this->getJson("/api/branches/{$branch->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'phone',
                    'address',
                    'type',
                    'active',
                    'client',
                ],
            ]);
    }

    public function test_can_update_branch()
    {
        $client = Client::factory()->create();
        $branch = Branch::factory()->create(['client_id' => $client->id]);

        $updateData = [
            'client_id' => $client->id,
            'name' => 'Sucursal Sur',
            'phone' => '555-5678',
            'address' => 'Av. Secundaria 456',
            'type' => 'Hospital',
            'active' => false,
        ];

        $response = $this->putJson("/api/branches/{$branch->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ]);

        $this->assertDatabaseHas('branches', [
            'id' => $branch->id,
            'name' => 'Sucursal Sur',
            'phone' => '555-5678',
            'active' => false,
        ]);
    }

    public function test_can_delete_branch_without_consulting_rooms()
    {
        $branch = Branch::factory()->create();

        $response = $this->deleteJson("/api/branches/{$branch->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
            ]);

        $this->assertSoftDeleted('branches', [
            'id' => $branch->id,
        ]);
    }

    public function test_cannot_delete_branch_with_consulting_rooms()
    {
        $branch = Branch::factory()->create();
        ConsultingRoom::factory()->create(['branch_id' => $branch->id]);

        $response = $this->deleteJson("/api/branches/{$branch->id}");

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Cannot delete branch. It has consulting rooms associated.',
            ]);
    }

    public function test_validation_errors_on_create()
    {
        $response = $this->postJson('/api/branches', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['client_id', 'name']);
    }

    public function test_can_search_branches()
    {
        $client = Client::factory()->create();
        Branch::factory()->create([
            'client_id' => $client->id,
            'name' => 'Sucursal Norte',
            'address' => 'Av. Principal 123',
        ]);
        Branch::factory()->create([
            'client_id' => $client->id,
            'name' => 'Sucursal Sur',
            'address' => 'Av. Secundaria 456',
        ]);

        $response = $this->getJson("/api/branches?client_id={$client->id}&search=Norte");

        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('data')));
    }

    public function test_client_filtering_for_show()
    {
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();
        $branch = Branch::factory()->create(['client_id' => $client1->id]);

        $response = $this->getJson("/api/branches/{$branch->id}?client_id={$client2->id}");

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Branch not found or access denied.',
            ]);
    }

    public function test_client_filtering_for_update()
    {
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();
        $branch = Branch::factory()->create(['client_id' => $client1->id]);

        $updateData = [
            'client_id' => $client2->id,
            'name' => 'Updated Branch',
        ];

        $response = $this->putJson("/api/branches/{$branch->id}?client_id={$client2->id}", $updateData);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Branch not found or access denied.',
            ]);
    }

    public function test_can_filter_by_active_status()
    {
        $client = Client::factory()->create();
        Branch::factory()->count(2)->create(['client_id' => $client->id, 'active' => true]);
        Branch::factory()->count(3)->create(['client_id' => $client->id, 'active' => false]);

        $response = $this->getJson("/api/branches?client_id={$client->id}&active=1");

        $response->assertStatus(200);
        $this->assertEquals(2, count($response->json('data')));
    }

    public function test_can_filter_by_type()
    {
        $client = Client::factory()->create();
        Branch::factory()->count(2)->create(['client_id' => $client->id, 'type' => 'Clínica']);
        Branch::factory()->count(3)->create(['client_id' => $client->id, 'type' => 'Hospital']);

        $response = $this->getJson("/api/branches?client_id={$client->id}&type=Clínica");

        $response->assertStatus(200);
        $this->assertEquals(2, count($response->json('data')));
    }

    public function test_branches_have_consulting_rooms_count()
    {
        $client = Client::factory()->create();
        $branch = Branch::factory()->create(['client_id' => $client->id]);
        ConsultingRoom::factory()->count(3)->create(['branch_id' => $branch->id]);

        $response = $this->getJson("/api/branches?client_id={$client->id}");

        $response->assertStatus(200);
        $branchData = $response->json('data')[0];
        $this->assertEquals(3, $branchData['consulting_rooms_count']);
    }
}
