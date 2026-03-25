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
        Schema::create('encounter_speciality_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('medical_speciality_id')
                ->index('ms_id_index');
            $table->foreign('medical_speciality_id', 'ms_id_foreign')
                ->references('id')
                ->on('medical_specialties');
            $table->string('question_esp', 250)->nullable();
            $table->string('question_eng', 250)->nullable();
            $table->string('options_esp', 1000)->nullable();
            $table->string('options_eng', 1000)->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->string('description_esp', 200)->nullable();
            $table->string('description_eng', 200)->nullable();
        });

        Schema::create('encounter_speciality_question_answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('encounter_id')
                ->index('enc_id_index');
            $table->foreign('encounter_id', 'enc_id_foreign')
                ->references('id')
                ->on('encounters')
                ->onDelete('cascade');
            $table->unsignedBigInteger('appointment_id')
                ->index('app_id_index');
            $table->foreign('appointment_id', 'app_id_foreign')
                ->references('id')
                ->on('appointments')
                ->onDelete('cascade');
            $table->unsignedBigInteger('created_by')
                ->index('usr_id_index')
                ->comment('creador del registro');
            $table->foreign('created_by', 'usr_id_foreign')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->unsignedBigInteger('encounter_speciality_question_id')
                ->index('esq_id_index');
            $table->foreign('encounter_speciality_question_id', 'esq_id_foreign')
                ->references('id')
                ->on('encounter_speciality_questions');
            $table->string('answer', 300);
            $table->softDeletes();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encounter_speciality_questions');
        Schema::dropIfExists('encounter_speciality_question_answers');
    }
};
