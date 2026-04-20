<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixCacheTagging extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:fix-tagging';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix cache tagging to support drivers without tag support';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $files = [
            app_path('Observers/PatientObserver.php'),
            app_path('Observers/MedicationRequestObserver.php'),
            app_path('Observers/EncounterObserver.php'),
            app_path('Observers/InvoiceObserver.php'),
            app_path('Observers/ClientSubscriptionObserver.php'),
            app_path('Livewire/Doctor/ConsultationEffectiveness.php'),
            app_path('Livewire/Doctor/YearlyAppointmentsChart.php'),
            app_path('Livewire/Doctor/PatientsByAgeBlock.php'),
            app_path('Livewire/Doctor/TopPrescribedMedications.php'),
            app_path('Livewire/Doctor/MonthlyAppointments.php'),
            app_path('Livewire/Dashboard/TopSpecialties.php'),
            app_path('Livewire/Dashboard/Counter.php'),
        ];

        $this->info('Fixing cache tagging in files...');

        foreach ($files as $file) {
            if (!File::exists($file)) {
                $this->warn("File not found: {$file}");
                continue;
            }

            $content = File::get($file);
            $originalContent = $content;

            // Add CacheHelper use statement if not present
            if (!str_contains($content, 'use App\Helpers\CacheHelper;')) {
                $content = preg_replace(
                    '/(namespace [^;]+;)/m',
                    "$1\n\nuse App\Helpers\CacheHelper;",
                    $content
                );
            }

            // Replace Cache::tags()->flush()
            $content = preg_replace(
                '/Cache::tags\(([^\)]+)\)->flush\(\)/m',
                'CacheHelper::flush($1)',
                $content
            );

            // Replace Cache::tags()->get()
            $content = preg_replace(
                '/Cache::tags\(([^\)]+)\)->get\(([^\)]+)\)/m',
                'CacheHelper::get($1, $2)',
                $content
            );

            // Replace Cache::tags()->put()
            $content = preg_replace(
                '/Cache::tags\(([^\)]+)\)->put\(([^,]+),\s*([^,]+),\s*([^\)]+)\)/m',
                'CacheHelper::put($1, $2, $3, $4)',
                $content
            );

            // Replace Cache::tags()->remember()
            $content = preg_replace(
                '/Cache::tags\(([^\)]+)\)->remember\(([^,]+),\s*([^,]+),\s*(function[^}]+\})\)/ms',
                'CacheHelper::remember($1, $2, $3, $4)',
                $content
            );

            if ($content !== $originalContent) {
                File::put($file, $content);
                $this->info("✓ Fixed: {$file}");
            } else {
                $this->comment("○ No changes needed: {$file}");
            }
        }

        $this->newLine();
        $this->info('Cache tagging fix completed!');
        $this->newLine();
        $this->comment('You can now use either:');
        $this->comment('1. CACHE_STORE=redis (recommended for production)');
        $this->comment('2. CACHE_STORE=database (will work with the fix)');

        return Command::SUCCESS;
    }
}
