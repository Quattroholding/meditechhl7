<?php

namespace Tests\Feature\Api;

use App\Models\Client;
use App\Models\Patient;
use App\Models\Practitioner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PractitionerAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles if they don't exist
        Role::firstOrCreate(['name' => 'doctor']);
        Role::firstOrCreate(['name' => 'paciente']);
    }

    public function test_practitioner_can_login()
    {
        // Create practitioner user
        $user = User::factory()->create([
            'email' => 'doctor@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user->assignRole('doctor');

        $practitioner = Practitioner::factory()->create([
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => 'Dr. John Doe',
            'phone' => '555-1234',
            'active' => true,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'doctor@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'token',
                'user_type',
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
                'practitioner' => [
                    'id',
                    'name',
                    'phone',
                    'active',
                    'clients',
                    'clients_count',
                ],
            ])
            ->assertJson([
                'user_type' => 'practitioner',
                'user' => [
                    'email' => 'doctor@example.com',
                ],
            ]);
    }

    public function test_patient_can_still_login()
    {
        // Create patient user
        $user = User::factory()->create([
            'email' => 'patient@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user->assignRole('paciente');

        $patient = Patient::factory()->create([
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => 'John Patient',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'patient@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'token',
                'user_type',
                'user',
                'patient',
            ])
            ->assertJson([
                'user_type' => 'patient',
            ]);
    }

    public function test_user_without_valid_role_cannot_login()
    {
        // Create user without valid role
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Don't assign any role or assign different role
        $user->assignRole('admin'); // Invalid role for mobile

        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_practitioner_without_profile_cannot_login()
    {
        // Create user with doctor role but no practitioner profile
        $user = User::factory()->create([
            'email' => 'doctor@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user->assignRole('doctor');
        // Don't create practitioner profile

        $response = $this->postJson('/api/auth/login', [
            'email' => 'doctor@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_practitioner_can_get_user_info()
    {
        $user = User::factory()->create();
        $user->assignRole('doctor');

        $practitioner = Practitioner::factory()->create([
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/auth/user');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user_type',
                'user',
                'practitioner',
            ])
            ->assertJson([
                'user_type' => 'practitioner',
            ]);
    }

    public function test_practitioner_can_refresh_token()
    {
        $user = User::factory()->create();
        $user->assignRole('doctor');

        $practitioner = Practitioner::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'token',
                'user_type',
            ])
            ->assertJson([
                'user_type' => 'practitioner',
            ]);
    }

    public function test_practitioner_can_logout()
    {
        $user = User::factory()->create();
        $user->assignRole('doctor');

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logout exitoso',
            ]);
    }

    public function test_practitioner_can_request_password_reset()
    {
        $user = User::factory()->create([
            'email' => 'doctor@example.com',
        ]);
        $user->assignRole('doctor');

        $practitioner = Practitioner::factory()->create([
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'doctor@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Se ha enviado un enlace de restablecimiento de contraseña a tu email.',
            ]);
    }

    public function test_invalid_credentials_return_error()
    {
        $user = User::factory()->create([
            'email' => 'doctor@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user->assignRole('doctor');

        $response = $this->postJson('/api/auth/login', [
            'email' => 'doctor@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_nonexistent_user_returns_error()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_validation_errors()
    {
        $response = $this->postJson('/api/auth/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'invalid-email',
            'password' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_user_without_role_gets_error_on_user_endpoint()
    {
        $user = User::factory()->create();
        // Don't assign any role

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/auth/user');

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Usuario sin rol válido.',
            ]);
    }

    public function test_mixed_user_types_work_correctly()
    {
        // Create both patient and practitioner
        $patientUser = User::factory()->create(['email' => 'patient@test.com']);
        $patientUser->assignRole('paciente');
        Patient::factory()->create(['user_id' => $patientUser->id]);

        $practitionerUser = User::factory()->create(['email' => 'doctor@test.com']);
        $practitionerUser->assignRole('doctor');
        Practitioner::factory()->create(['user_id' => $practitionerUser->id]);

        // Test patient user endpoint
        $response = $this->actingAs($patientUser, 'sanctum')
            ->getJson('/api/auth/user');

        $response->assertStatus(200)
            ->assertJson(['user_type' => 'patient']);

        // Test practitioner user endpoint
        $response = $this->actingAs($practitionerUser, 'sanctum')
            ->getJson('/api/auth/user');

        $response->assertStatus(200)
            ->assertJson(['user_type' => 'practitioner']);
    }

    public function test_practitioner_login_includes_multiple_clients()
    {
        // Create practitioner user
        $user = User::factory()->create([
            'email' => 'doctor@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user->assignRole('doctor');

        $practitioner = Practitioner::factory()->create([
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => 'Dr. Multi Client',
        ]);

        // Create multiple clients
        $client1 = Client::factory()->create(['name' => 'Hospital A']);
        $client2 = Client::factory()->create(['name' => 'Clinica B']);
        $client3 = Client::factory()->create(['name' => 'Centro C']);

        // Associate user with multiple clients
        $user->clients()->attach([$client1->id, $client2->id, $client3->id]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'doctor@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'practitioner' => [
                    'clients' => [
                        '*' => [
                            'id',
                            'name',
                            'phone',
                            'address',
                        ],
                    ],
                    'clients_count',
                ],
            ])
            ->assertJson([
                'practitioner' => [
                    'clients_count' => 3,
                ],
            ]);

        // Verify specific clients are included
        $responseData = $response->json();
        $clientNames = collect($responseData['practitioner']['clients'])->pluck('name')->toArray();

        $this->assertContains('Hospital A', $clientNames);
        $this->assertContains('Clinica B', $clientNames);
        $this->assertContains('Centro C', $clientNames);
    }

    public function test_practitioner_with_no_clients_has_empty_list()
    {
        // Create practitioner user without client associations
        $user = User::factory()->create([
            'email' => 'doctor@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user->assignRole('doctor');

        $practitioner = Practitioner::factory()->create([
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        // Don't associate with any clients

        $response = $this->postJson('/api/auth/login', [
            'email' => 'doctor@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'practitioner' => [
                    'clients' => [],
                    'clients_count' => 0,
                ],
            ]);
    }

    public function test_practitioner_user_endpoint_includes_clients()
    {
        $user = User::factory()->create();
        $user->assignRole('doctor');

        $practitioner = Practitioner::factory()->create([
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        // Create and associate clients
        $client1 = Client::factory()->create(['name' => 'Test Hospital']);
        $client2 = Client::factory()->create(['name' => 'Test Clinic']);
        $user->clients()->attach([$client1->id, $client2->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/auth/user');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'practitioner' => [
                    'clients' => [
                        '*' => [
                            'id',
                            'name',
                            'phone',
                            'address',
                        ],
                    ],
                    'clients_count',
                ],
            ])
            ->assertJson([
                'practitioner' => [
                    'clients_count' => 2,
                ],
            ]);
    }

    public function test_patient_login_does_not_include_clients()
    {
        // Create patient user (should not have clients list)
        $user = User::factory()->create([
            'email' => 'patient@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user->assignRole('paciente');

        $patient = Patient::factory()->create([
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'patient@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'patient' => [
                    'id',
                    'name',
                    'phone',
                    // Should NOT have clients or clients_count
                ],
            ]);

        // Verify clients keys don't exist for patients
        $responseData = $response->json();
        $this->assertArrayNotHasKey('clients', $responseData['patient']);
        $this->assertArrayNotHasKey('clients_count', $responseData['patient']);
    }
}
