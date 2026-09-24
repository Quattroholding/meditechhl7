<?php

namespace App\Console\Commands;

use App\Services\EncounterProcessingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessEncounterVoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'encounter:process-voice {encounterId} {transcription?}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Process a voice transcription and populate encounter SOAP sections using EncounterProcessingAgent';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $encounterId = (int) $this->argument('encounterId');
        $transcription = $this->argument('transcription');

        if (! $transcription) {
            $this->line('Interactive mode: Enter transcription (press Ctrl+D to finish):');
            $transcription = '';
            while (true) {
                $line = fgets(STDIN);
                if ($line === false) {
                    break;
                }
                $transcription .= $line;
            }
            $transcription = trim($transcription);
        }

        if (empty($transcription)) {
            $this->error('Transcription cannot be empty');

            return self::FAILURE;
        }

        $this->info("Processing encounter {$encounterId} with transcription (".strlen($transcription).' characters)...');

        try {
            $service = app(EncounterProcessingService::class);
            $result = $service->processVoiceTranscription($encounterId, $transcription);

            if ($result['success']) {
                $this->success('Processing completed successfully!');

                $processingResult = $result['processing_result'] ?? [];

                $this->info('Results:');
                $this->line('Status: '.$processingResult['processing_status'] ?? 'unknown');
                $this->line('Sections completed: '.implode(', ', $processingResult['sections_completed'] ?? []));

                if (isset($processingResult['statistics'])) {
                    $this->line('Statistics:');
                    foreach ($processingResult['statistics'] as $key => $value) {
                        $this->line("  - {$key}: {$value}");
                    }
                }

                if (isset($processingResult['warnings']) && ! empty($processingResult['warnings'])) {
                    $this->warn('Warnings:');
                    foreach ($processingResult['warnings'] as $warning) {
                        $this->line("  - {$warning}");
                    }
                }

                // Display final summary
                $summary = $service->getEncounterSummary($encounterId);
                if ($summary['success']) {
                    $this->info('Encounter Summary:');
                    $this->table(
                        ['Section', 'Item Count'],
                        [
                            ['Present Illnesses', $summary['summary']['subjective_items']['present_illnesses'] ?? 0],
                            ['Vital Signs', $summary['summary']['objective_items']['vital_signs'] ?? 0],
                            ['Physical Exams', $summary['summary']['objective_items']['physical_exams'] ?? 0],
                            ['Diagnoses', $summary['summary']['assessment_items']['diagnoses'] ?? 0],
                            ['Medications', $summary['summary']['plan_items']['medications'] ?? 0],
                            ['Service Requests', $summary['summary']['plan_items']['service_requests'] ?? 0],
                            ['Total SOAP Items', $summary['summary']['total_soap_items'] ?? 0],
                        ]
                    );
                }

                return self::SUCCESS;
            } else {
                $this->error('Processing failed: '.$result['message'] ?? 'Unknown error');
                Log::error('ProcessEncounterVoice command failed', $result);

                return self::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());
            Log::error('ProcessEncounterVoice command error: '.$e->getMessage(), [
                'encounter_id' => $encounterId,
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }
    }
}
