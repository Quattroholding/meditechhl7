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
        Schema::create('hospitalizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_history_id')->constrained('medical_histories')->cascadeOnDelete();
            $table->date('admission_date');
            $table->date('discharge_date')->nullable();
            $table->string('facility')->comment('Nombre del centro hospitalario');
            $table->string('reason')->comment('Motivo de hospitalización');
            $table->text('diagnosis')->nullable();
            $table->text('procedures_performed')->nullable();
            $table->text('outcome')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['medical_history_id', 'admission_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospitalizations');
    }
};
