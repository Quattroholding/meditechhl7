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
        Schema::create('present_illness_symptoms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('present_illness_id')->constrained('present_illnesses')->cascadeOnDelete();
            $table->string('code')->comment('Código SNOMED del síntoma');
            $table->string('display')->comment('Descripción del síntoma');
            $table->string('severity')->nullable();
            $table->string('duration')->nullable();
            $table->string('body_site')->nullable()->comment('Localización anatómica');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['present_illness_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('present_illness_symptoms');
    }
};
