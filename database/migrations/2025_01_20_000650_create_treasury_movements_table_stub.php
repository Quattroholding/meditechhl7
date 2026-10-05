<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Crear tabla stub - será completada en sprints posteriores
        if (! Schema::hasTable('treasury_movements')) {
            Schema::create('treasury_movements', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
                $table->string('movement_number', 50)->unique();
                $table->date('movement_date');
                $table->enum('movement_type', ['income', 'expense', 'transfer'])->default('income');
                $table->string('source_type', 100)->nullable();
                $table->unsignedBigInteger('source_id')->nullable();
                $table->decimal('amount', 15, 2);
                $table->text('description')->nullable();
                $table->timestamps();
                $table->index(['client_id', 'movement_date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('treasury_movements');
    }
};
