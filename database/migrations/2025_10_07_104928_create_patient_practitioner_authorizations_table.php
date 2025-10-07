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
        Schema::create('patient_practitioner_authorizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('practitioner_id')->constrained('practitioners')->onDelete('cascade');

            // Nombre corto para la foreign key debido al límite de 64 caracteres en MySQL
            $table->unsignedBigInteger('authorized_by_user_id')->nullable()->comment('Usuario que autorizó el acceso');
            $table->foreign('authorized_by_user_id', 'ppa_authorized_by_user_fk')
                ->references('id')->on('users')->onDelete('set null');

            $table->timestamp('authorized_at')->nullable()->comment('Fecha y hora de autorización');
            $table->softDeletes();
            $table->timestamps();

            // Índice único para evitar duplicados
            $table->unique(['patient_id', 'practitioner_id'], 'ppa_patient_practitioner_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_practitioner_authorizations');
    }
};
