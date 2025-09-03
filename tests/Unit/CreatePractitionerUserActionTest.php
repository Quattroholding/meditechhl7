<?php

namespace Tests\Unit;

use App\Actions\CreatePractitionerUserAction;
use App\Models\Client;
use App\Models\Practitioner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreatePractitionerUserActionTest extends TestCase
{
    use RefreshDatabase;

    protected CreatePractitionerUserAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new CreatePractitionerUserAction;

        // Seed roles and permissions
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_creates_user_for_practitioner_successfully(): void
    {
        // Create users first to avoid null reference in BaseModel
        $adminUser = User::factory()->create();
        $client = Client::factory()->create();
        $adminUser->update(['default_client_id' => $client->id]);
        $this->actingAs($adminUser);

        // Create practitioner without factory configure() to avoid automatic user creation
        $practitioner = new Practitioner([
            'email' => 'doctor@example.com',
            'given_name' => 'John',
            'family_name' => 'Doe',
            'client_id' => $client->id,
            'fhir_id' => 'practitioner-test-123',
            'identifier' => '12345678',
            'name' => 'Dr. John Doe',
            'gender' => 'male',
            'birth_date' => '1980-01-01',
            'registry' => '12345678',
            'active' => true,
        ]);
        $practitioner->save();

        $result = $this->action->execute($practitioner);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('doctor@example.com', $result->email);
        $this->assertEquals('John', $result->first_name);
        $this->assertEquals('Doe', $result->last_name);
        $this->assertTrue($result->hasRole('doctor'));
        $this->assertTrue(Hash::check($result->temporary_password, $result->password));

        $practitioner->refresh();
        $this->assertEquals($result->id, $practitioner->user_id);
    }

    public function test_throws_exception_if_practitioner_already_has_user(): void
    {
        $existingUser = User::factory()->create();
        $practitioner = new Practitioner([
            'user_id' => $existingUser->id,
            'email' => 'doctor@example.com',
            'given_name' => 'Jane',
            'family_name' => 'Doe',
            'fhir_id' => 'practitioner-test-456',
            'identifier' => '87654321',
            'name' => 'Dr. Jane Doe',
            'registry' => '87654321',
            'active' => true,
        ]);
        $practitioner->save();

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('El profesional ya tiene un usuario asignado');

        $this->action->execute($practitioner);
    }

    public function test_throws_exception_if_practitioner_has_no_email(): void
    {
        $practitioner = new Practitioner([
            'email' => null,
            'given_name' => 'No',
            'family_name' => 'Email',
            'fhir_id' => 'practitioner-test-789',
            'identifier' => '11111111',
            'name' => 'Dr. No Email',
            'registry' => '11111111',
            'active' => true,
        ]);
        $practitioner->save();

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('El profesional debe tener un email para crear un usuario');

        $this->action->execute($practitioner);
    }

    public function test_throws_exception_if_email_already_exists(): void
    {
        User::factory()->create(['email' => 'doctor@example.com']);
        $practitioner = new Practitioner([
            'email' => 'doctor@example.com',
            'given_name' => 'Duplicate',
            'family_name' => 'Email',
            'fhir_id' => 'practitioner-test-000',
            'identifier' => '22222222',
            'name' => 'Dr. Duplicate Email',
            'registry' => '22222222',
            'active' => true,
        ]);
        $practitioner->save();

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Ya existe un usuario con este email');

        $this->action->execute($practitioner);
    }
}
