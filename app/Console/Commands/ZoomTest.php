<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Patient;
use App\Models\Practitioner;
use App\Services\ZoomService;
use Illuminate\Console\Command;

class ZoomTest extends Command
{
    protected $signature = 'zoom:test {action?} {--appointment_id=}';
    protected $description = 'Test Zoom integration (Sandbox mode for development)';

    private ZoomService $zoomService;

    public function __construct()
    {
        parent::__construct();
        $this->zoomService = app(ZoomService::class);
    }

    public function handle()
    {
        $this->info('╔════════════════════════════════════════╗');
        $this->info('║     ZOOM TESTING - '.$this->zoomService->getMode().' MODE     ║');
        $this->info('╚════════════════════════════════════════╝');
        $this->newLine();

        if ($this->zoomService->isSandboxMode()) {
            $this->warn('⚠️  SANDBOX MODE ACTIVE');
            $this->line('   - No real Zoom API calls');
            $this->line('   - Simulated meetings for testing');
            $this->line('   - No credentials required');
            $this->newLine();
        } else {
            $this->info('✅ PRODUCTION MODE ACTIVE');
            $this->line('   - Using real Zoom API');
            $this->line('   - Real meetings created');
            $this->newLine();
        }

        $action = $this->argument('action') ?? 'menu';

        match ($action) {
            'create' => $this->testCreateMeeting(),
            'list' => $this->listAppointments(),
            'simulate' => $this->simulateWebhooks(),
            'config' => $this->showConfig(),
            'menu' => $this->showMenu(),
            default => $this->error("Unknown action: $action"),
        };
    }

    private function showMenu(): void
    {
        $this->line('Available commands:');
        $this->line('');
        $this->line('  <fg=green>zoom:test create</>           Create a test meeting');
        $this->line('  <fg=green>zoom:test list</>             List appointments');
        $this->line('  <fg=green>zoom:test simulate</>         Simulate webhook events');
        $this->line('  <fg=green>zoom:test config</>           Show Zoom configuration');
        $this->line('');
        $this->line('Example:');
        $this->line('  <fg=cyan>php artisan zoom:test create</>');
        $this->line('  <fg=cyan>php artisan zoom:test simulate --appointment_id=1</>');
    }

    private function testCreateMeeting(): void
    {
        $this->info('Creating test meeting...');

        try {
            // Create test data if needed
            $client = Client::first() ?? Client::factory()->create(['name' => 'Test Clinic']);
            $patient = Patient::first() ?? Patient::factory()->create();
            $practitioner = Practitioner::first() ?? Practitioner::factory()->create();

            // Create appointment
            $appointment = Appointment::factory()->create([
                'client_id' => $client->id,
                'patient_id' => $patient->id,
                'practitioner_id' => $practitioner->id,
                'consultation_type' => 'virtual',
            ]);

            $this->line('');
            $this->info('Creating Zoom meeting for appointment...');

            // Create meeting
            $meeting = $this->zoomService->createMeeting($appointment);

            $this->newLine();
            $this->info('✅ Meeting created successfully!');
            $this->table(
                ['Field', 'Value'],
                [
                    ['Appointment ID', $appointment->id],
                    ['Meeting ID', $meeting['meeting_id']],
                    ['Join URL', $meeting['join_url']],
                    ['Password', $meeting['password']],
                    ['Mode', $this->zoomService->getMode()],
                ]
            );

            $this->newLine();
            $this->line('<fg=cyan>Test next step:</>');
            $this->line('  <fg=green>php artisan zoom:test simulate --appointment_id='.$appointment->id.'</>');

        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());
        }
    }

    private function listAppointments(): void
    {
        $appointments = Appointment::where('consultation_type', 'virtual')
            ->with('patient', 'practitioner')
            ->latest()
            ->limit(10)
            ->get();

        if ($appointments->isEmpty()) {
            $this->warn('No virtual appointments found');
            return;
        }

        $this->table(
            ['ID', 'Patient', 'Doctor', 'Start', 'Meeting ID', 'Status'],
            $appointments->map(fn ($a) => [
                $a->id,
                $a->patient->name,
                $a->practitioner->name,
                $a->start->format('Y-m-d H:i'),
                $a->virtual_room_id ?? 'NOT SET',
                $a->virtual_session_started_at ? 'ACTIVE' : 'PENDING',
            ])
        );
    }

    private function simulateWebhooks(): void
    {
        $appointmentId = $this->option('appointment_id')
            ?? $this->ask('Enter appointment ID');

        $appointment = Appointment::find($appointmentId);

        if (!$appointment) {
            $this->error("Appointment #$appointmentId not found");
            return;
        }

        if (!$appointment->virtual_room_id) {
            $this->error('Appointment has no meeting ID. Create one first:');
            $this->line('  php artisan zoom:test create');
            return;
        }

        $this->info('Simulating webhook events for appointment #'.$appointmentId);
        $this->newLine();

        // Event 1: meeting.started
        $this->info('1️⃣  Simulating: meeting.started');
        $this->zoomService->handleWebhookEvent([
            'event' => 'meeting.started',
            'payload' => ['object' => ['id' => $appointment->virtual_room_id]],
        ]);
        $appointment->refresh();
        $this->line('   ✓ Started at: '.$appointment->virtual_session_started_at);

        $this->newLine();

        // Event 2: meeting.ended
        $this->info('2️⃣  Simulating: meeting.ended');
        $this->zoomService->handleWebhookEvent([
            'event' => 'meeting.ended',
            'payload' => [
                'object' => [
                    'id' => $appointment->virtual_room_id,
                    'participant_count' => 2,
                ],
            ],
        ]);
        $appointment->refresh();
        $this->line('   ✓ Ended at: '.$appointment->virtual_session_ended_at);
        $this->line('   ✓ Participants: '.$appointment->virtual_session_metadata['participant_count']);

        $this->newLine();
        $this->info('✅ All webhook events simulated!');
    }

    private function showConfig(): void
    {
        $this->info('Zoom Configuration:');
        $this->newLine();

        $this->table(
            ['Setting', 'Value', 'Status'],
            [
                ['Mode', $this->zoomService->getMode(), $this->zoomService->isSandboxMode() ? '🧪' : '✅'],
                ['Configured', $this->zoomService->isConfigured() ? 'Yes' : 'No', $this->zoomService->isConfigured() ? '✅' : '⚠️'],
                ['Account ID', $this->maskValue(config('services.zoom.account_id')), $this->checkValue(config('services.zoom.account_id'))],
                ['Client ID', $this->maskValue(config('services.zoom.client_id')), $this->checkValue(config('services.zoom.client_id'))],
                ['Client Secret', $this->maskValue(config('services.zoom.client_secret')), $this->checkValue(config('services.zoom.client_secret'))],
                ['Host User ID', config('services.zoom.host_user_id') ?: 'NOT SET', $this->checkValue(config('services.zoom.host_user_id'))],
                ['Webhook Secret', $this->maskValue(config('services.zoom.webhook_secret')), $this->checkValue(config('services.zoom.webhook_secret'))],
                ['Data Center', config('services.zoom.data_center') ?: 'US', '✅'],
                ['Sandbox Mode', config('services.zoom.sandbox_mode') ? 'Enabled' : 'Disabled', '✅'],
            ]
        );

        $this->newLine();
        $this->info('How to use sandbox mode:');
        $this->line('  Add to .env:');
        $this->line('  <fg=yellow>ZOOM_SANDBOX_MODE=true</>');
        $this->line('');
        $this->line('  Then test without real credentials:');
        $this->line('  <fg=cyan>php artisan zoom:test create</>');
    }

    private function maskValue(?string $value): string
    {
        if (!$value) {
            return 'NOT SET';
        }

        return substr($value, 0, 4).'...'.substr($value, -4);
    }

    private function checkValue(?string $value): string
    {
        return $value ? '✅' : '❌';
    }
}
