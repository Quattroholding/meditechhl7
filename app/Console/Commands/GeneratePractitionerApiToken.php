<?php

namespace App\Console\Commands;

use App\Models\ApiToken;
use App\Models\Practitioner;
use Illuminate\Console\Command;

class GeneratePractitionerApiToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:generate-token
                            {practitioner_id : The ID of the practitioner}
                            {--name= : Name/description for the token}
                            {--scopes=* : Scopes for the token (default: hemoscreen)}
                            {--allowed-ips=* : Allowed IP addresses (default: *)}
                            {--expires-days= : Days until expiration (default: never)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate an API token for a practitioner to use with HemoScreen gateway';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $practitionerId = $this->argument('practitioner_id');

        // Find practitioner
        $practitioner = Practitioner::withoutGlobalScopes()->find($practitionerId);

        if (! $practitioner) {
            $this->error("Practitioner with ID {$practitionerId} not found.");

            return self::FAILURE;
        }

        // Get token details
        $name = $this->option('name') ?? "HemoScreen Gateway - {$practitioner->name}";
        $scopes = $this->option('scopes');
        if (empty($scopes)) {
            $scopes = ['hemoscreen'];
        }

        $allowedIps = $this->option('allowed-ips');
        if (empty($allowedIps)) {
            $allowedIps = ['*'];
        }

        $expiresDays = $this->option('expires-days');
        $expiresAt = $expiresDays ? now()->addDays($expiresDays) : null;

        // Generate token
        $token = ApiToken::generateToken();

        // Create API token
        $apiToken = ApiToken::create([
            'practitioner_id' => $practitioner->id,
            'name' => $name,
            'token' => $token,
            'scopes' => $scopes,
            'allowed_ips' => $allowedIps,
            'expires_at' => $expiresAt,
            'active' => true,
            'created_by' => 'console',
            'description' => 'Token for HemoScreen gateway integration',
        ]);

        // Display success message
        $this->info('✅ API Token generated successfully!');
        $this->newLine();

        $this->table(
            ['Field', 'Value'],
            [
                ['Token ID', $apiToken->id],
                ['Practitioner', $practitioner->name." (ID: {$practitioner->id})"],
                ['Token Name', $apiToken->name],
                ['Scopes', implode(', ', $apiToken->scopes)],
                ['Allowed IPs', implode(', ', $apiToken->allowed_ips)],
                ['Expires At', $apiToken->expires_at?->format('Y-m-d H:i:s') ?? 'Never'],
                ['Created At', $apiToken->created_at->format('Y-m-d H:i:s')],
            ]
        );

        $this->newLine();
        $this->warn('🔐 API Token (save this securely, it will not be shown again):');
        $this->line($token);
        $this->newLine();

        $this->info('📋 Example usage in gateway configuration:');
        $this->line('Authorization: Bearer '.$token);
        $this->newLine();

        $this->info('📋 Example curl request:');
        $this->line('curl -X POST '.config('app.url').'/api/v1/lab/hemoscreen \\');
        $this->line('  -H "Authorization: Bearer '.$token.'" \\');
        $this->line('  -H "Content-Type: application/json" \\');
        $this->line('  -d \'{"patient_identifier": "123456789", "observations": [{"loinc": "718-7", "value": "14.5", "unit": "g/dL"}]}\'');
        $this->newLine();
        $this->comment('Note: clinic_id is automatically obtained from the practitioner\'s user default_client_id');

        return self::SUCCESS;
    }
}
