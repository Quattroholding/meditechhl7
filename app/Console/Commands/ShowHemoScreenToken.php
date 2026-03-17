<?php

namespace App\Console\Commands;

use App\Models\ApiToken;
use App\Models\Practitioner;
use Illuminate\Console\Command;

class ShowHemoScreenToken extends Command
{
    protected $signature = 'hemoscreen:show-token
                            {practitioner_id : The ID of the practitioner}';

    protected $description = 'Display the HemoScreen API token for a practitioner';

    public function handle(): int
    {
        $practitionerId = $this->argument('practitioner_id');

        $practitioner = Practitioner::with('user')->find($practitionerId);

        if (! $practitioner) {
            $this->error("Practitioner with ID {$practitionerId} not found.");
            return self::FAILURE;
        }

        $tokens = ApiToken::where('practitioner_id', $practitionerId)
            ->where('mode', 'standalone')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($tokens->isEmpty()) {
            $this->warn("No standalone tokens found for this practitioner.");
            return self::FAILURE;
        }

        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('  HEMOSCREEN STANDALONE API TOKENS');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->newLine();

        $this->table(
            ['Field', 'Value'],
            [
                ['Practitioner', $practitioner->name],
                ['Email', $practitioner->email],
                ['User ID', $practitioner->user_id],
                ['Practitioner ID', $practitioner->id],
            ]
        );

        $this->newLine();

        foreach ($tokens as $token) {
            $this->info("Token: {$token->name}");
            $this->table(
                ['Field', 'Value'],
                [
                    ['Token', $token->token],
                    ['Mode', $token->mode],
                    ['Scopes', implode(', ', $token->scopes)],
                    ['Allowed IPs', empty($token->allowed_ips) ? 'All IPs (*)' : implode(', ', $token->allowed_ips)],
                    ['Expires', $token->expires_at?->format('Y-m-d H:i:s') ?? 'Never'],
                    ['Status', $token->active ? '✓ Active' : '✗ Inactive'],
                    ['Created', $token->created_at->format('Y-m-d H:i:s')],
                ]
            );
            $this->newLine();
        }

        $this->info('API Endpoint Configuration:');
        $this->info('URL: https://webhooks.meditecpty.com/api/v1/lab/hemoscreen-standlone');
        $this->info('Method: POST');
        $this->info('Authorization: Bearer <token>');

        return self::SUCCESS;
    }
}
