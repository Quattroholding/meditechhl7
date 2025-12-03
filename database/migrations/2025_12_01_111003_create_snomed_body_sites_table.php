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
        Schema::create('snomed_body_sites', function (Blueprint $table) {
            $table->id();
            $table->string('snomed_code')->unique()->comment('SNOMED CT code');
            $table->string('display')->comment('Human-readable description');
            $table->string('display_es')->nullable()->comment('Spanish description');
            $table->string('category')->nullable()->comment('Body region category');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('category');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snomed_body_sites');
    }
};
