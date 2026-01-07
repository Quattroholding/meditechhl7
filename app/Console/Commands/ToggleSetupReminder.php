<?php

namespace App\Console\Commands;

use App\Models\SetupReminderConfig;
use Illuminate\Console\Command;

class ToggleSetupReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:toggle
                            {key? : The reminder key (subscription, branch, etc.)}
                            {--activate : Activate the reminder}
                            {--deactivate : Deactivate the reminder}
                            {--list : List all reminders}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate or deactivate setup reminders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // List all reminders
        if ($this->option('list')) {
            $this->listReminders();

            return 0;
        }

        $key = $this->argument('key');

        if (! $key) {
            $this->error('Please provide a reminder key or use --list to see all reminders.');

            return 1;
        }

        $reminder = SetupReminderConfig::where('reminder_key', $key)->first();

        if (! $reminder) {
            $this->error("Reminder '{$key}' not found.");
            $this->info('Available reminders: subscription, branch, consulting_room, patient, service_catalog, working_hours, rapid_access');

            return 1;
        }

        // Activate
        if ($this->option('activate')) {
            $reminder->update(['is_active' => true]);
            $this->info("✓ Reminder '{$key}' has been activated.");

            return 0;
        }

        // Deactivate
        if ($this->option('deactivate')) {
            $reminder->update(['is_active' => false]);
            $this->info("✓ Reminder '{$key}' has been deactivated.");

            return 0;
        }

        // Toggle
        $newState = ! $reminder->is_active;
        $reminder->update(['is_active' => $newState]);
        $status = $newState ? 'activated' : 'deactivated';
        $this->info("✓ Reminder '{$key}' has been {$status}.");

        return 0;
    }

    protected function listReminders(): void
    {
        $reminders = SetupReminderConfig::orderBy('order')->get();

        $this->info('Setup Reminders Configuration:');
        $this->newLine();

        $rows = [];
        foreach ($reminders as $reminder) {
            $rows[] = [
                $reminder->order,
                $reminder->reminder_key,
                $reminder->title,
                $reminder->is_required ? '🔴 Required' : '💡 Suggestion',
                $reminder->is_active ? '✓ Active' : '✗ Inactive',
            ];
        }

        $this->table(
            ['Order', 'Key', 'Title', 'Type', 'Status'],
            $rows
        );
    }
}
