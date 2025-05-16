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
        Schema::create('encounter_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name',100);
            $table->string('name_esp',100);
            $table->string('table_list',60)->nullable();
            $table->string('table_list_filter',100)->nullable();
            $table->string('livewire_component_name',150)->nullable();
            $table->json('livewire_component_fields')->nullable();
            $table->string('category',50)->nullable();
            $table->integer('medical_speciality_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('rapid_access', function (Blueprint $table) {
            $table->foreignId('encounter_section_id')->nullable()->constrained('encounter_sections');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encounter_sections');

        Schema::table('rapid_access', function (Blueprint $table) {
            $table->dropColumn('encounter_section_id');
        });
    }
};
