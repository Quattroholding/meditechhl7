<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\ClientSubscription;
use App\Models\Package;
use App\Models\Practitioner;
use App\Models\User;
use App\Models\UserClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateStandaloneToFullSami extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hemoscreen:migrate-to-full-sami
                            {practitioner_id : The ID of the standalone practitioner to migrate}
                            {--package-id=1 : Package ID for the new subscription}
                            {--trial-days=15 : Trial days for the new subscription}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate a standalone HemoScreen user to full SAMI with subscription';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        return DB::transaction(function () {
            $practitionerId = $this->argument('practitioner_id');
            $packageId = $this->option('package-id');
            $trialDays = (int) $this->option('trial-days');

            // 1. Find and validate practitioner
            $practitioner = Practitioner::with('user')->find($practitionerId);

            if (! $practitioner) {
                $this->error("Practitioner with ID {$practitionerId} not found.");

                return self::FAILURE;
            }

            if (! $practitioner->isStandalone()) {
                $this->error("Practitioner {$practitioner->name} is not in standalone mode.");
                $this->info('This command is only for migrating standalone HemoScreen users.');

                return self::FAILURE;
            }

            $user = $practitioner->user;
            if (! $user) {
                $this->error('Practitioner has no associated user account.');

                return self::FAILURE;
            }

            $oldClientId = $practitioner->scb_id;
            $standaloneResultsCount = $practitioner->standaloneResults()->count();

            // 2. Validate package
            $package = Package::find($packageId);
            if (! $package) {
                $this->error("Package with ID {$packageId} not found.");

                return self::FAILURE;
            }

            $this->info('═══════════════════════════════════════════════════════════════');
            $this->info('  STANDALONE HEMOSCREEN TO FULL SAMI MIGRATION');
            $this->info('═══════════════════════════════════════════════════════════════');
            $this->newLine();

            $this->table(
                ['Field', 'Value'],
                [
                    ['Practitioner', $practitioner->name],
                    ['Practitioner ID', $practitioner->id],
                    ['User', $user->email],
                    ['Current Client ID', $oldClientId],
                    ['Standalone Results', $standaloneResultsCount],
                    ['Package', $package->name],
                    ['Trial Days', $trialDays],
                ]
            );

            $this->newLine();
            if (! $this->confirm('Do you want to proceed with the migration?', true)) {
                $this->info('Migration cancelled.');

                return self::SUCCESS;
            }

            // 3. Create new Client for full SAMI
            $client = Client::create([
                'name' => $practitioner->name,
                'long_name' => $practitioner->name,
                'email' => $user->email,
                'whatsapp' => $practitioner->phone,
                'package_id' => $packageId,
                'ruc' => 'MIGRATED-'.time(),
                'dv' => 0,
                'active' => true,
            ]);

            $this->info("✓ Created new client: {$client->name} (ID: {$client->id})");

            // 4. Update user
            $user->default_client_id = $client->id;
            $user->active = true;

            // Remove hemoscreen role and add admin client role
            if ($user->hasRole('hemoscreen')) {
                $user->removeRole('hemoscreen');
            }

            if (! $user->hasRole('admin client')) {
                $user->assignRole('admin client');
            }

            $user->save();

            $this->info("✓ Updated user: {$user->email}");

            // 5. Add new client relationship (keep old one for historical data)
            if (! $user->clients()->where('client_id', $client->id)->exists()) {
                UserClient::create([
                    'user_id' => $user->id,
                    'client_id' => $client->id,
                ]);

                $this->info('✓ Added new client relationship');
            }

            // 6. Update practitioner to enable full SAMI features
            $practitioner->update([
                'is_standalone' => false,
                'scb_id' => $client->id,
            ]);

            $this->info('✓ Migrated practitioner to full SAMI mode');

            // 7. Create subscription with trial
            $subscription = ClientSubscription::create([
                'client_id' => $client->id,
                'package_id' => $packageId,
                'status' => 'trialing',
                'trial_start' => now(),
                'trial_end' => now()->addDays($trialDays),
                'current_period_start' => now(),
                'current_period_end' => now()->addDays($trialDays),
            ]);

            $this->info("✓ Created subscription with {$trialDays} days trial");

            // 8. Summary
            $this->newLine();
            $this->info('═══════════════════════════════════════════════════════════════');
            $this->info('  MIGRATION COMPLETED SUCCESSFULLY');
            $this->info('═══════════════════════════════════════════════════════════════');
            $this->newLine();

            $this->table(
                ['Item', 'Details'],
                [
                    ['Status', '✓ Migration Successful'],
                    ['New Client ID', $client->id],
                    ['Old Client ID (Standalone)', $oldClientId],
                    ['Subscription Status', $subscription->status],
                    ['Trial Period', "{$trialDays} days"],
                    ['Historical Results', "{$standaloneResultsCount} results preserved"],
                ]
            );

            $this->newLine();
            $this->info('User Features:');
            $this->info('  ✓ Can access full SAMI clinic management');
            $this->info('  ✓ Can still view historical standalone HemoScreen results');
            $this->info('  ✓ Can create patients, appointments, encounters');
            $this->info('  ✓ Can use integrated HemoScreen with consultations');

            return self::SUCCESS;
        });
    }
}
