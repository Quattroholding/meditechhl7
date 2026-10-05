<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('document_parse_results', function (Blueprint $table) {
            // Drop the old integer column and create a new decimal column
            $table->dropColumn('processing_cost_cents');
        });

        Schema::table('document_parse_results', function (Blueprint $table) {
            // Add new column with decimal type for clarity (stores dollars)
            $table->decimal('processing_cost_usd', 10, 4)->nullable()->after('output_tokens')->comment('Processing cost in USD dollars');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_parse_results', function (Blueprint $table) {
            $table->dropColumn('processing_cost_usd');
            $table->integer('processing_cost_cents')->nullable()->after('output_tokens')->comment('Estimated cost in cents (hundredths of USD)');
        });
    }
};
