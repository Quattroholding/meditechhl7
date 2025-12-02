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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->comment('FHIR Media resource ID');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('encounter_id')->nullable()->constrained('encounters')->onDelete('set null');
            $table->foreignId('practitioner_id')->nullable()->constrained('practitioners');

            // Polymorphic relationship - can be attached to different resources
            $table->morphs('subject'); // subject_id, subject_type (BodyStructure, SkinLesion, etc.)

            $table->string('identifier')->nullable()->unique();
            $table->enum('status', ['preparation', 'in-progress', 'not-done', 'on-hold', 'stopped', 'completed', 'entered-in-error', 'unknown'])->default('completed');

            // Media type and content
            $table->string('type')->comment('photo, video, audio, etc.');
            $table->string('modality')->nullable()->comment('Acquisition modality: dermoscopy, photograph, etc.');
            $table->string('view')->nullable()->comment('anterior, posterior, close-up, etc.');

            // File information
            $table->string('content_type')->comment('MIME type: image/jpeg, image/png, etc.');
            $table->string('file_path')->comment('Storage path to the file');
            $table->string('file_name')->comment('Original filename');
            $table->unsignedBigInteger('file_size')->nullable()->comment('File size in bytes');
            $table->integer('width')->nullable()->comment('Image width in pixels');
            $table->integer('height')->nullable()->comment('Image height in pixels');
            $table->integer('frames')->nullable()->comment('Number of frames for video');
            $table->decimal('duration', 10, 2)->nullable()->comment('Duration in seconds for video/audio');

            // Clinical context
            $table->text('note')->nullable()->comment('Clinical notes about this media');
            $table->dateTime('created_datetime')->comment('When the media was created/captured');
            $table->dateTime('issued_datetime')->nullable()->comment('When the media was issued');

            // FHIR extensions
            $table->json('device_name')->nullable()->comment('Device used to capture (camera, dermoscope, etc.)');
            $table->json('extension')->nullable()->comment('FHIR extensions');

            $table->timestamps();
            $table->softDeletes();

            // Indexes (morphs() already creates index for subject_type, subject_id)
            $table->index(['patient_id', 'type']);
            $table->index('encounter_id');
            $table->index('created_datetime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
