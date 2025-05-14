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
        Schema::create('family_histories', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->comment('FHIR FamilyMemberHistory resource ID');
            $table->foreignId('patient_id')->constrained('patients');
            $table->string('relationship')->comment('Parentesco');
            $table->boolean('deceased')->default(false);
            $table->integer('age_at_death')->nullable();
            $table->json('condition')->nullable()->comment('Condiciones médicas del familiar');
            $table->text('note')->nullable();
            $table->json('extension')->nullable()->comment('Extensiones FHIR');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_histories');
    }
};
