<?php

namespace App\Console\Commands;

use App\Models\ApiToken;
use App\Models\Practitioner;
use Illuminate\Console\Command;

class GenerateHemoScreenStandaloneToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hemoscreen:generate-standalone-token
                            {practitioner_id : The ID of the standalone practitioner}
                            {--name=HemoScreen Device : Name for the API token}
                            {--allowed-ips=* : Comma-separated list of allowed IPs or * for all}
                            {--expires-days=365 : Token expiration in days (0 for no expiration)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new API token for standalone HemoScreen practitioner';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $practitionerId = $this->argument('practitioner_id');

        // Find practitioner
        $practitioner = Practitioner::find($practitionerId);

        if (! $practitioner) {
            $this->error("Practitioner with ID {$practitionerId} not found.");

            return self::FAILURE;
        }

        // Verify practitioner is standalone
        if (! $practitioner->isStandalone()) {
            $this->error("Practitioner {$practitioner->name} is not configured as standalone.");
            $this->info('Only standalone practitioners can have standalone tokens.');

            return self::FAILURE;
        }

        // Get token parameters
        $tokenName = $this->option('name');
        $allowedIps = $this->option('allowed-ips');
        $expiresInDays = (int) $this->option('expires-days');

        $allowedIpsArray = $allowedIps === '*' ? ['*'] : array_map('trim', explode(',', $allowedIps));

        // Create API Token
        $apiToken = ApiToken::create([
            'practitioner_id' => $practitioner->id,
            'name' => $tokenName,
            'token' => ApiToken::generateToken(),
            'scopes' => ['hemoscreen-standalone'],
            'mode' => 'standalone',
            'allowed_ips' => $allowedIpsArray,
            'expires_at' => $expiresInDays > 0 ? now()->addDays($expiresInDays) : null,
            'active' => true,
            'description' => "Standalone HemoScreen token for {$practitioner->name}",
        ]);

        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('    STANDALONE HEMOSCREEN TOKEN GENERATED');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->newLine();

        $this->table(
            ['Field', 'Value'],
            [
                ['Token', $apiToken->token],
                ['Practitioner', $practitioner->name],
                ['Practitioner ID', $practitioner->id],
                ['Token Name', $apiToken->name],
                ['Mode', $apiToken->mode],
                ['Scopes', implode(', ', $apiToken->scopes)],
                ['Allowed IPs', implode(', ', $apiToken->allowed_ips)],
                ['Expires', $apiToken->expires_at?->format('Y-m-d H:i:s') ?? 'Never'],
                ['Status', $apiToken->active ? 'Active' : 'Inactive'],
            ]
        );

        $this->newLine();
        $this->info('Configuration:');
        $this->info('  API Endpoint: '.url('/api/v1/lab/hemoscreen-standalone'));
        $this->info('  Authorization: Bearer '.$apiToken->token);

        return self::SUCCESS;
    }
}
