<?php

namespace App\Console\Commands;

use App\Models\ApiToken;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class GenerateApiToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:generate-token 
                            {name : Nombre descriptivo del token}
                            {--ips=* : IPs permitidas (usar * para todas las IPs)}
                            {--scopes=* : Alcances/permisos del token (usar * para todos los permisos)}
                            {--expires= : Fecha de expiración (Y-m-d format)}
                            {--description= : Descripción del propósito del token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generar un nuevo token API con restricciones de IP y permisos específicos';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $ips = $this->option('ips') ?: ['*'];
        $scopes = $this->option('scopes') ?: ['*'];
        $expires = $this->option('expires');
        $description = $this->option('description');

        // Validate inputs
        if (! $this->validateInputs($name, $ips, $expires)) {
            return Command::FAILURE;
        }

        // Generate token
        $token = ApiToken::generateToken();

        try {
            $apiToken = ApiToken::create([
                'name' => $name,
                'token' => $token,
                'allowed_ips' => $ips,
                'scopes' => $scopes,
                'expires_at' => $expires ? Carbon::parse($expires) : null,
                'description' => $description,
                'created_by' => 'console',
                'active' => true,
            ]);

            $this->displayTokenInfo($apiToken);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error creando el token: '.$e->getMessage());

            return Command::FAILURE;
        }
    }

    /**
     * Validate command inputs
     */
    private function validateInputs(string $name, array $ips, ?string $expires): bool
    {
        // Validate name
        if (empty(trim($name))) {
            $this->error('El nombre del token no puede estar vacío');

            return false;
        }

        // Check if name already exists
        if (ApiToken::where('name', $name)->exists()) {
            $this->error('Ya existe un token con ese nombre');

            return false;
        }

        // Validate IPs
        foreach ($ips as $ip) {
            if ($ip !== '*' && ! $this->isValidIpOrCidr($ip)) {
                $this->error("IP inválida: {$ip}");

                return false;
            }
        }

        // Validate expiration date
        if ($expires) {
            $validator = Validator::make(['expires' => $expires], [
                'expires' => 'date|after:now',
            ]);

            if ($validator->fails()) {
                $this->error('Fecha de expiración inválida. Use formato Y-m-d y debe ser una fecha futura');

                return false;
            }
        }

        return true;
    }

    /**
     * Check if IP or CIDR is valid
     */
    private function isValidIpOrCidr(string $ip): bool
    {
        // Check for CIDR notation
        if (strpos($ip, '/') !== false) {
            [$subnet, $bits] = explode('/', $ip);

            return filter_var($subnet, FILTER_VALIDATE_IP) &&
                   is_numeric($bits) &&
                   $bits >= 0 &&
                   $bits <= 32;
        }

        // Check regular IP
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Display token information
     */
    private function displayTokenInfo(ApiToken $apiToken): void
    {
        $this->info('✅ Token API creado exitosamente!');
        $this->newLine();

        $this->table(
            ['Campo', 'Valor'],
            [
                ['Nombre', $apiToken->name],
                ['Token', $apiToken->token],
                ['IPs Permitidas', implode(', ', $apiToken->allowed_ips)],
                ['Permisos', implode(', ', $apiToken->scopes)],
                ['Expira', $apiToken->expires_at ? $apiToken->expires_at->format('Y-m-d H:i:s') : 'Nunca'],
                ['Descripción', $apiToken->description ?: 'N/A'],
                ['Estado', 'Activo'],
            ]
        );

        $this->newLine();
        $this->warn('⚠️  IMPORTANTE: Guarda este token en un lugar seguro. No podrás verlo nuevamente.');
        $this->newLine();

        // Show usage examples
        $this->comment('📖 Ejemplos de uso:');
        $this->line('Header Authorization: Bearer '.$apiToken->token);
        $this->line('Header X-API-Token: '.$apiToken->token);
        $this->line('Query parameter: ?api_token='.$apiToken->token);
    }
}
