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
        Schema::create('encounter_template', function (Blueprint $table) {
            $table->id();
            $table->string('type',15)->comment('MASTER , CLIENT , USER');
            $table->foreignId('client_id')->nullable()->references('id')->on('clients')->onDelete('cascade')->comment('Client Owner if type CLIENT');
            $table->foreignId('user_id')->nullable()->references('id')->on('users')->onDelete('cascade')->comment('User Owner if type USER');
            $table->foreignId('encounter_section_id')->nullable()->references('id')->on('encounter_sections')->onDelete('cascade')->comment('Secion Available');
            $table->json('encounter_section_fields')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encounter_template');
    }
};
