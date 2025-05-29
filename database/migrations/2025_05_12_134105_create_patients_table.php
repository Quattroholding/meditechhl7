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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->comment('FHIR Patient resource ID');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('identifier')->unique()->comment('Identificador único del paciente');
            $table->enum('identifier_type', ['DNI', 'Pasaporte', 'Seguro', 'Otro'])->nullable();
            $table->string('name');
            $table->string('given_name');
            $table->string('family_name');
            $table->enum('gender', ['male', 'female', 'other', 'unknown'])->default('unknown');
            $table->date('birth_date')->nullable();
            $table->boolean('deceased')->default(false)->nullable();
            $table->dateTime('deceased_date')->nullable();
            $table->string('address')->nullable();
            $table->string('billing_address')->nullable();
            $table->string('city',100)->nullable();
            $table->string('state',100)->nullable();
            $table->string('postal_code',5)->nullable();
            $table->string('country',75)->nullable();
            $table->string('phone',20)->nullable();
            $table->string('email',50)->nullable();
            $table->string('blood_type',5)->nullable();
            $table->string('marital_status')->nullable();
            $table->boolean('multiple_birth')->default(false)->nullable();
            $table->integer('multiple_birth_count')->nullable();
            $table->json('communication')->nullable()->comment('Idiomas que habla el paciente');
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
        Schema::dropIfExists('patients');
    }
};
