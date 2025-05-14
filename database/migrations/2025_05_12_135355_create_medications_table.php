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
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->string('fhir_id')->unique()->comment('FHIR Medication resource ID');
            $table->string('code')->comment('Código del medicamento (RxNorm)');
            $table->string('name');
            $table->string('form')->nullable();
            $table->string('dose')->nullable();
            $table->string('strength')->nullable();
            $table->json('ingredient')->nullable();
            $table->json('extension')->nullable()->comment('Extensiones FHIR');
            $table->string('product_type',100)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
