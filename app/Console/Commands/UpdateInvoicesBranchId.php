<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class UpdateInvoicesBranchId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:update-branch-id {--dry-run : Run without actually updating the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing invoices with branch_id from their associated appointment consulting room';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('Running in DRY RUN mode - no data will be updated');
        }

        $this->info('Starting to update invoices with branch_id...');

        // Get all invoices without branch_id
        $invoices = Invoice::whereNull('branch_id')
            ->with(['encounter.appointment.consultingRoom'])
            ->get();

        $this->info("Found {$invoices->count()} invoices without branch_id");

        $bar = $this->output->createProgressBar($invoices->count());
        $bar->start();

        $updated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($invoices as $invoice) {
            try {
                $branchId = $invoice->encounter?->appointment?->consultingRoom?->branch_id;

                if ($branchId) {
                    if (! $dryRun) {
                        $invoice->update(['branch_id' => $branchId]);
                    }
                    $updated++;
                } else {
                    $skipped++;
                    $this->newLine();
                    $this->warn("Invoice {$invoice->invoice_number} (ID: {$invoice->id}) - No branch_id found in appointment chain");
                }
            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->error("Error updating invoice {$invoice->invoice_number} (ID: {$invoice->id}): {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info('=== Summary ===');
        $this->info("Total invoices processed: {$invoices->count()}");
        $this->info("Successfully updated: {$updated}");
        $this->warn("Skipped (no branch found): {$skipped}");

        if ($errors > 0) {
            $this->error("Errors: {$errors}");
        }

        if ($dryRun) {
            $this->newLine();
            $this->comment('This was a DRY RUN. No data was actually updated.');
            $this->comment('Run without --dry-run flag to update the database.');
        }

        return Command::SUCCESS;
    }
}
