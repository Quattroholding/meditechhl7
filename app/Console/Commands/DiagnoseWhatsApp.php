<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DiagnoseWhatsApp extends Command
{
    protected $signature = 'whatsapp:diagnose';

    protected $description = 'Diagnose WhatsApp configuration and connectivity';

    public function handle(): int
    {
        $this->info('🔍 Diagnosticando configuración de WhatsApp...');
        $this->newLine();

        $hasErrors = false;

        // 1. Check APP_URL
        $this->info('1️⃣  Verificando APP_URL...');
        $appUrl = config('app.url');
        $this->line("   APP_URL: {$appUrl}");

        if (str_contains($appUrl, '.test') || str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1')) {
            $this->error('   ❌ APP_URL es local - WhatsApp Meta API NO puede acceder a este dominio');
            $this->warn('   💡 Solución: Cambiar APP_URL a un dominio público HTTPS o usar ngrok');
            $hasErrors = true;
        } elseif (! str_starts_with($appUrl, 'https://')) {
            $this->error('   ❌ APP_URL debe usar HTTPS');
            $hasErrors = true;
        } else {
            $this->info('   ✅ APP_URL es público y usa HTTPS');
        }

        $this->newLine();

        // 2. Check Meta credentials
        $this->info('2️⃣  Verificando credenciales de Meta...');
        $phoneNumberId = config('services.meta.whatsapp_phone_number_id');
        $accessToken = config('services.meta.whatsapp_access_token');

        if (! $phoneNumberId) {
            $this->error('   ❌ META_WHATSAPP_PHONE_NUMBER_ID no configurado');
            $hasErrors = true;
        } else {
            $this->info("   ✅ Phone Number ID: {$phoneNumberId}");
        }

        if (! $accessToken) {
            $this->error('   ❌ META_WHATSAPP_ACCESS_TOKEN no configurado');
            $hasErrors = true;
        } else {
            $masked = substr($accessToken, 0, 20).'...'.substr($accessToken, -10);
            $this->info("   ✅ Access Token: {$masked}");
        }

        $this->newLine();

        // 3. Check testing mode
        $this->info('3️⃣  Verificando modo de testing...');
        $testingMode = config('services.meta.testing_mode');
        $testingPhone = config('services.meta.testing_phone');

        if ($testingMode) {
            $this->warn('   ⚠️  Modo de testing: ACTIVADO');
            $this->line("   📱 Número de prueba: {$testingPhone}");
            $this->warn('   💡 Todos los mensajes se enviarán a este número');
        } else {
            $this->info('   ✅ Modo de testing: DESACTIVADO (producción)');
        }

        $this->newLine();

        // 4. Check storage link
        $this->info('4️⃣  Verificando enlace de storage...');
        if (is_link(public_path('storage'))) {
            $this->info('   ✅ Enlace simbólico de storage existe');
        } else {
            $this->error('   ❌ Enlace simbólico de storage NO existe');
            $this->warn('   💡 Ejecutar: php artisan storage:link');
            $hasErrors = true;
        }

        $this->newLine();

        // 5. Check temp directory
        $this->info('5️⃣  Verificando directorio temporal...');
        $tempPath = 'temp/whatsapp';
        if (Storage::disk('public')->exists($tempPath)) {
            $files = Storage::disk('public')->files($tempPath);
            $this->info('   ✅ Directorio temporal existe');
            $this->line('   📁 Archivos temporales: '.count($files));
        } else {
            $this->warn('   ⚠️  Directorio temporal no existe (se creará al enviar primer mensaje)');
        }

        $this->newLine();

        // 6. Test URL accessibility (only if not local)
        if (! str_contains($appUrl, '.test') && ! str_contains($appUrl, 'localhost')) {
            $this->info('6️⃣  Probando accesibilidad de URLs...');

            try {
                $testUrl = $appUrl.'/storage/temp/test.txt';
                $response = Http::timeout(5)->get($appUrl);

                if ($response->successful()) {
                    $this->info('   ✅ Dominio es accesible públicamente');
                } else {
                    $this->warn("   ⚠️  Dominio retorna código {$response->status()}");
                }
            } catch (\Exception $e) {
                $this->error('   ❌ No se puede acceder al dominio: '.$e->getMessage());
                $hasErrors = true;
            }
        } else {
            $this->warn('6️⃣  Omitiendo prueba de accesibilidad (dominio local)');
        }

        $this->newLine();

        // 7. Test Meta API connectivity
        if ($phoneNumberId && $accessToken) {
            $this->info('7️⃣  Probando conectividad con Meta API...');

            try {
                $response = Http::timeout(10)
                    ->withToken($accessToken)
                    ->get("https://graph.facebook.com/v21.0/{$phoneNumberId}");

                if ($response->successful()) {
                    $data = $response->json();
                    $this->info('   ✅ Conexión exitosa con Meta API');
                    $this->line('   📱 Número de negocio: '.($data['display_phone_number'] ?? 'N/A'));
                    $this->line('   🔢 ID verificado: '.($data['id'] ?? 'N/A'));
                } else {
                    $this->error('   ❌ Error al conectar con Meta API');
                    $this->line('   Código: '.$response->status());
                    $this->line('   Respuesta: '.$response->body());
                    $hasErrors = true;
                }
            } catch (\Exception $e) {
                $this->error('   ❌ Excepción al conectar: '.$e->getMessage());
                $hasErrors = true;
            }
        }

        $this->newLine();
        $this->newLine();

        // Summary
        if ($hasErrors) {
            $this->error('❌ Se encontraron problemas en la configuración');
            $this->newLine();
            $this->warn('📖 Lee WHATSAPP-TESTING-SETUP.md para soluciones detalladas');

            return Command::FAILURE;
        } else {
            $this->info('✅ Configuración de WhatsApp correcta');
            $this->info('🎉 El sistema debería poder enviar mensajes de WhatsApp');

            return Command::SUCCESS;
        }
    }
}
