<?php

namespace App\Console\Commands;

use App\Models\ApiToken;
use App\Models\Client;
use App\Models\Practitioner;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class CreateHemoScreenStandaloneUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hemoscreen:create-standalone-user
                            {--name= : Full name of the practitioner}
                            {--email= : Email address for login}
                            {--identifier= : Professional identifier (e.g., medical license)}
                            {--password= : Password (will be generated if not provided)}
                            {--token-name=HemoScreen Device : Name for the API token}
                            {--allowed-ips=* : Comma-separated list of allowed IPs or * for all}
                            {--expires-days=365 : Token expiration in days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a standalone HemoScreen user with practitioner, client, and API token';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        return DB::transaction(function () {
            // Get or prompt for required information
            $name = $this->option('name') ?: $this->ask('Practitioner full name');
            $email = $this->option('email') ?: $this->ask('Email address');
            $identifier = $this->option('identifier') ?: $this->ask('Professional identifier (e.g., 8-111-2222)');
            $password = $this->option('password') ?: Str::random(16);

            // Validate email doesn't already exist
            if (User::where('email', $email)->exists()) {
                $this->error("Email {$email} already exists. Use a different email or migrate the existing user.");

                return self::FAILURE;
            }

            // Check if hemoscreen role exists
            $hemoscreenRole = Role::where('name', 'hemoscreen')->first();
            if (! $hemoscreenRole) {
                $this->error('HemoScreen role not found. Run: php artisan db:seed --class=HemoScreenStandaloneRoleSeeder');

                return self::FAILURE;
            }

            $this->info('Creating standalone HemoScreen user...');

            // 1. Create minimal Client for standalone user
            $client = Client::create([
                'name' => "HemoScreen - {$name}",
                'long_name' => "HemoScreen Standalone Client for {$name}",
                'email' => $email,
                'hemoscreen_only' => true,
                'active' => true,
            ]);

            $this->info("✓ Created client: {$client->name} (ID: {$client->id})");

            // 2. Create User
            $user = User::create([
                'first_name' => explode(' ', $name)[0],
                'last_name' => implode(' ', array_slice(explode(' ', $name), 1)),
                'email' => $email,
                'password' => Hash::make($password),
                'default_client_id' => $client->id,
                'active' => true,
            ]);

            // Assign hemoscreen role
            $user->assignRole('hemoscreen');

            // Link user to client
            $user->clients()->attach($client->id);

            $this->info("✓ Created user: {$user->email} (ID: {$user->id})");

            // 3. Create Practitioner
            $practitioner = Practitioner::create([
                'fhir_id' => 'practitioner-'.Str::uuid(),
                'identifier' => $identifier,
                'name' => $name,
                'given_name' => $user->first_name,
                'family_name' => $user->last_name,
                'email' => $email,
                'user_id' => $user->id,
                'active' => true,
                'is_standalone' => true,
                'registry' => 'STANDALONE',
                'scb_id' => $client->id,
            ]);

            $this->info("✓ Created practitioner: {$practitioner->name} (ID: {$practitioner->id})");

            // 4. Generate API Token
            $tokenName = $this->option('token-name');
            $allowedIps = $this->option('allowed-ips');
            $expiresInDays = (int) $this->option('expires-days');

            // Handle allowed IPs (can be string or array)
            if (is_array($allowedIps)) {
                $allowedIpsArray = $allowedIps;
            } else {
                $allowedIpsArray = $allowedIps === '*' ? ['*'] : array_map('trim', explode(',', $allowedIps));
            }

            $apiToken = ApiToken::create([
                'practitioner_id' => $practitioner->id,
                'name' => $tokenName,
                'token' => ApiToken::generateToken(),
                'scopes' => ['hemoscreen-standalone'],
                'mode' => 'standalone',
                'allowed_ips' => $allowedIpsArray,
                'expires_at' => now()->addDays($expiresInDays),
                'active' => true,
                'description' => "Standalone HemoScreen token for {$name}",
            ]);

            $this->info("✓ Created API token: {$tokenName}");

            // Display summary
            $this->newLine();
            $this->info('═══════════════════════════════════════════════════════════════');
            $this->info('    STANDALONE HEMOSCREEN USER CREATED SUCCESSFULLY');
            $this->info('═══════════════════════════════════════════════════════════════');
            $this->newLine();

            $this->table(
                ['Field', 'Value'],
                [
                    ['User ID', $user->id],
                    ['Practitioner ID', $practitioner->id],
                    ['Client ID', $client->id],
                    ['Name', $name],
                    ['Email', $email],
                    ['Password', $this->option('password') ? '[provided]' : $password],
                    ['Identifier', $identifier],
                    ['Role', 'hemoscreen'],
                ]
            );

            $this->newLine();
            $this->info('API Token Configuration:');
            $this->table(
                ['Field', 'Value'],
                [
                    ['Token', $apiToken->token],
                    ['Name', $apiToken->name],
                    ['Mode', $apiToken->mode],
                    ['Scopes', implode(', ', $apiToken->scopes)],
                    ['Allowed IPs', implode(', ', $apiToken->allowed_ips)],
                    ['Expires', $apiToken->expires_at?->format('Y-m-d H:i:s') ?? 'Never'],
                ]
            );

            $this->newLine();
            $this->info('Next Steps:');
            $this->info('1. Configure HemoScreen gateway device with the token above');
            $this->info('2. Set API endpoint: '.url('/api/v1/lab/hemoscreen-standalone'));
            $this->info('3. User can log in at: '.url('/login'));
            $this->info('4. Dashboard available at: '.url('/hemoscreen/dashboard'));

            if (! $this->option('password')) {
                $this->newLine();
                $this->warn("⚠ Generated password: {$password}");
                $this->warn('  Please share this password securely with the user.');
            }

            return self::SUCCESS;
        });
    }
}
