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
        Schema::create('practitioners', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->comment('FHIR Practitioner resource ID');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('identifier')->unique()->comment('Número de colegiado/licencia');
            $table->string('name');
            $table->string('given_name');
            $table->string('family_name');
            $table->enum('gender', ['male', 'female', 'other', 'unknown']);
            $table->date('birth_date')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->json('qualification')->nullable()->comment('Especialidades y calificaciones');
            $table->json('communication')->nullable()->comment('Idiomas que habla el médico');
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('practitioners');
    }
};
