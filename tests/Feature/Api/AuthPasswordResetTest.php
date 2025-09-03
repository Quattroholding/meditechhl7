<?php

namespace Tests\Feature\Api;

use App\Models\Patient;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_sends_reset_link_to_patient()
    {
        // Crear roles si no existen
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

        Notification::fake();

        // Crear un usuario paciente
        $user = User::factory()->asPatient()->create([
            'email' => 'patient@example.com',
        ]);

        // Crear el registro de paciente
        Patient::factory()->create([
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->first_name.' '.$user->last_name,
        ]);

        // Realizar la petición
        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'patient@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Se ha enviado un enlace de restablecimiento de contraseña a tu email.',
            ]);

        // Verificar que se envió la notificación
        Notification::assertSentTo(
            $user,
            ResetPasswordNotification::class
        );
    }

    public function test_forgot_password_fails_for_non_patient_user()
    {
        // Crear roles si no existen
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

        // Crear un usuario doctor (no paciente)
        $user = User::factory()->asDoctor()->create([
            'email' => 'doctor@example.com',
        ]);

        // Realizar la petición
        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'doctor@example.com',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Este email no corresponde a un paciente.',
            ]);
    }

    public function test_forgot_password_fails_for_non_existent_email()
    {
        // Realizar la petición con email que no existe
        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'No se encontró un usuario con este email.',
            ]);
    }

    public function test_forgot_password_validates_email_format()
    {
        // Realizar la petición con email inválido
        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
