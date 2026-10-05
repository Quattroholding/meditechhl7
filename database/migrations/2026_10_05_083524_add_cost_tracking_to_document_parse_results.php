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
            $table->string('model_used')->nullable()->after('detected_format')->comment('Model used for parsing (e.g., claude-sonnet-5)');
            $table->integer('input_tokens')->nullable()->after('model_used')->comment('Input tokens used by Claude API');
            $table->integer('output_tokens')->nullable()->after('input_tokens')->comment('Output tokens used by Claude API');
            $table->integer('total_tokens')->nullable()->after('output_tokens')->comment('Total tokens (input + output)');
            $table->integer('processing_cost_cents')->nullable()->after('total_tokens')->comment('Estimated cost in cents (hundredths of USD)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_parse_results', function (Blueprint $table) {
            $table->dropColumn([
                'model_used',
                'input_tokens',
                'output_tokens',
                'total_tokens',
                'processing_cost_cents',
            ]);
        });
    }
};
