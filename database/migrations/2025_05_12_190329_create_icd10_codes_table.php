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
        Schema::create('icd10_codes', function (Blueprint $table) {
            $table->id();
            $table->longText('description');
            $table->longText('description_es')->nullable();
            $table->string('code',15);
            $table->string('icd10_code',15)->nullable();
            $table->boolean('active')->default(1);
            $table->foreignId('medical_speciality_id')->nullable()->references('id')->on('medical_specialties')->nullable()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->references('id')->on('users')->onDelete('cascade')->comment('Created by');

            $table->text('note')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('icd10_codes');
    }
};
