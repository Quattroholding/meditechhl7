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
        Schema::create('document_parse_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_upload_id')->unique()->constrained('document_uploads')->onDelete('cascade');
            $table->longText('raw_response'); // JSON - respuesta completa de Google Document AI
            $table->longText('extracted_data'); // JSON - datos estructurados y editables
            $table->decimal('confidence_score', 3, 2)->nullable(); // 0.00 - 1.00
            $table->boolean('has_warnings')->default(false);
            $table->boolean('has_errors')->default(false);
            $table->longText('validation_messages')->nullable(); // JSON array de warnings/errors
            $table->boolean('manually_edited')->default(false);
            $table->foreignId('edited_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['document_upload_id']);
            $table->index(['confidence_score']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_parse_results');
    }
};
