<?php

namespace Tests\Feature\Api;

use App\Models\Branch;
use App\Models\Client;
use App\Models\ConsultingRoom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultingRoomTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
    }

    public function test_can_create_consulting_room()
    {
        $client = Client::factory()->create();
        $branch = Branch::factory()->create(['client_id' => $client->id]);

        $consultingRoomData = [
            'branch_id' => $branch->id,
            'name' => 'Consultorio 1',
            'number' => '101',
            'floor' => '1',
            'active' => true,
        ];

        $response = $this->postJson('/api/consulting-rooms', $consultingRoomData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'number',
                    'floor',
                    'active',
                    'branch',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('consulting_rooms', [
            'branch_id' => $branch->id,
            'name' => 'Consultorio 1',
            'number' => '101',
            'active' => true,
        ]);
    }

    public function test_can_list_consulting_rooms_with_branch_filter()
    {
        $client = Client::factory()->create();
        $branch1 = Branch::factory()->create(['client_id' => $client->id]);
        $branch2 = Branch::factory()->create(['client_id' => $client->id]);

        ConsultingRoom::factory()->count(3)->create(['branch_id' => $branch1->id]);
        ConsultingRoom::factory()->count(2)->create(['branch_id' => $branch2->id]);

        $response = $this->getJson("/api/consulting-rooms?branch_id={$branch1->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'number',
                        'floor',
                        'active',
                        'branch_name',
                    ],
                ],
                'links',
                'meta',
            ]);

        $this->assertEquals(3, count($response->json('data')));
    }

    public function test_can_list_consulting_rooms_by_client_filter()
    {
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();
        $branch1 = Branch::factory()->create(['client_id' => $client1->id]);
        $branch2 = Branch::factory()->create(['client_id' => $client2->id]);

        ConsultingRoom::factory()->count(3)->create(['branch_id' => $branch1->id]);
        ConsultingRoom::factory()->count(2)->create(['branch_id' => $branch2->id]);

        $response = $this->getJson("/api/consulting-rooms?client_id={$client1->id}");

        $response->assertStatus(200);
        $this->assertEquals(3, count($response->json('data')));
    }

    public function test_can_list_consulting_rooms_by_branch_endpoint()
    {
        $client = Client::factory()->create();
        $branch = Branch::factory()->create(['client_id' => $client->id]);
        ConsultingRoom::factory()->count(3)->create(['branch_id' => $branch->id, 'active' => true]);
        ConsultingRoom::factory()->create(['branch_id' => $branch->id, 'active' => false]); // Inactive

        $response = $this->getJson("/api/branches/{$branch->id}/consulting-rooms");

        $response->assertStatus(200);
        $this->assertEquals(3, count($response->json('data'))); // Only active consulting rooms
    }

    public function test_can_list_consulting_rooms_by_client_endpoint()
    {
        $client = Client::factory()->create();
        $branch = Branch::factory()->create(['client_id' => $client->id]);
        ConsultingRoom::factory()->count(3)->create(['branch_id' => $branch->id, 'active' => true]);
        ConsultingRoom::factory()->create(['branch_id' => $branch->id, 'active' => false]); // Inactive

        $response = $this->getJson("/api/clients/{$client->id}/consulting-rooms");

        $response->assertStatus(200);
        $this->assertEquals(3, count($response->json('data'))); // Only active consulting rooms
    }

    public function test_can_show_consulting_room()
    {
        $consultingRoom = ConsultingRoom::factory()->create();

        $response = $this->getJson("/api/consulting-rooms/{$consultingRoom->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'number',
                    'floor',
                    'active',
                    'branch',
                ],
            ]);
    }

    public function test_can_update_consulting_room()
    {
        $client = Client::factory()->create();
        $branch = Branch::factory()->create(['client_id' => $client->id]);
        $consultingRoom = ConsultingRoom::factory()->create(['branch_id' => $branch->id]);

        $updateData = [
            'branch_id' => $branch->id,
            'name' => 'Consultorio Actualizado',
            'number' => '202',
            'floor' => '2',
            'active' => false,
        ];

        $response = $this->putJson("/api/consulting-rooms/{$consultingRoom->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
            ]);

        $this->assertDatabaseHas('consulting_rooms', [
            'id' => $consultingRoom->id,
            'name' => 'Consultorio Actualizado',
            'number' => '202',
            'active' => false,
        ]);
    }

    public function test_can_delete_consulting_room()
    {
        $consultingRoom = ConsultingRoom::factory()->create();

        $response = $this->deleteJson("/api/consulting-rooms/{$consultingRoom->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
            ]);

        $this->assertSoftDeleted('consulting_rooms', [
            'id' => $consultingRoom->id,
        ]);
    }

    public function test_validation_errors_on_create()
    {
        $response = $this->postJson('/api/consulting-rooms', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['branch_id', 'name']);
    }

    public function test_can_search_consulting_rooms()
    {
        $client = Client::factory()->create();
        $branch = Branch::factory()->create(['client_id' => $client->id]);
        ConsultingRoom::factory()->create([
            'branch_id' => $branch->id,
            'name' => 'Consultorio A',
            'number' => '101',
        ]);
        ConsultingRoom::factory()->create([
            'branch_id' => $branch->id,
            'name' => 'Consultorio B',
            'number' => '102',
        ]);

        $response = $this->getJson("/api/consulting-rooms?branch_id={$branch->id}&search=A");

        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('data')));
    }

    public function test_client_filtering_for_show()
    {
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();
        $branch = Branch::factory()->create(['client_id' => $client1->id]);
        $consultingRoom = ConsultingRoom::factory()->create(['branch_id' => $branch->id]);

        $response = $this->getJson("/api/consulting-rooms/{$consultingRoom->id}?client_id={$client2->id}");

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Consulting room not found or access denied.',
            ]);
    }

    public function test_client_filtering_for_update()
    {
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();
        $branch1 = Branch::factory()->create(['client_id' => $client1->id]);
        $branch2 = Branch::factory()->create(['client_id' => $client2->id]);
        $consultingRoom = ConsultingRoom::factory()->create(['branch_id' => $branch1->id]);

        $updateData = [
            'branch_id' => $branch2->id,
            'name' => 'Updated Consulting Room',
        ];

        $response = $this->putJson("/api/consulting-rooms/{$consultingRoom->id}?client_id={$client2->id}", $updateData);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Consulting room not found or access denied.',
            ]);
    }

    public function test_can_filter_by_active_status()
    {
        $client = Client::factory()->create();
        $branch = Branch::factory()->create(['client_id' => $client->id]);
        ConsultingRoom::factory()->count(2)->create(['branch_id' => $branch->id, 'active' => true]);
        ConsultingRoom::factory()->count(3)->create(['branch_id' => $branch->id, 'active' => false]);

        $response = $this->getJson("/api/consulting-rooms?branch_id={$branch->id}&active=1");

        $response->assertStatus(200);
        $this->assertEquals(2, count($response->json('data')));
    }

    public function test_can_filter_by_floor()
    {
        $client = Client::factory()->create();
        $branch = Branch::factory()->create(['client_id' => $client->id]);
        ConsultingRoom::factory()->count(2)->create(['branch_id' => $branch->id, 'floor' => '1']);
        ConsultingRoom::factory()->count(3)->create(['branch_id' => $branch->id, 'floor' => '2']);

        $response = $this->getJson("/api/consulting-rooms?branch_id={$branch->id}&floor=1");

        $response->assertStatus(200);
        $this->assertEquals(2, count($response->json('data')));
    }

    public function test_consulting_rooms_include_branch_information()
    {
        $client = Client::factory()->create(['name' => 'Test Client']);
        $branch = Branch::factory()->create(['client_id' => $client->id, 'name' => 'Test Branch']);
        $consultingRoom = ConsultingRoom::factory()->create(['branch_id' => $branch->id]);

        $response = $this->getJson("/api/consulting-rooms?branch_id={$branch->id}");

        $response->assertStatus(200);
        $consultingRoomData = $response->json('data')[0];
        $this->assertEquals('Test Branch', $consultingRoomData['branch_name']);
    }

    public function test_can_filter_consulting_rooms_by_client_and_branch()
    {
        $client = Client::factory()->create();
        $branch1 = Branch::factory()->create(['client_id' => $client->id]);
        $branch2 = Branch::factory()->create(['client_id' => $client->id]);

        ConsultingRoom::factory()->count(2)->create(['branch_id' => $branch1->id, 'active' => true]);
        ConsultingRoom::factory()->count(3)->create(['branch_id' => $branch2->id, 'active' => true]);

        $response = $this->getJson("/api/clients/{$client->id}/consulting-rooms?branch_id={$branch1->id}");

        $response->assertStatus(200);
        $this->assertEquals(2, count($response->json('data')));
    }
}
