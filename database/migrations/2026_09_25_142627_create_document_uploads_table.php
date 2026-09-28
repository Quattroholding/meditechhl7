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
        Schema::create('document_uploads', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('document_type'); // Stored as string for enum compatibility
            $table->string('status')->default('pending'); // pending, parsing, parsed, parsing_failed, approved, rejected, processing, processed, processing_failed
            $table->string('file_path');
            $table->string('original_filename');
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('mime_type')->default('application/pdf');
            $table->foreignId('uploaded_by_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('processing_started_at')->nullable();
            $table->timestamp('processing_completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices para consultas frecuentes
            $table->index(['client_id', 'status']);
            $table->index(['document_type', 'status']);
            $table->index(['uploaded_by_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_uploads');
    }
};
