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
        Schema::create('survey_question_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_response_id')->constrained()->onDelete('cascade');
            $table->foreignId('survey_question_id')->constrained()->onDelete('cascade');
            $table->text('answer_text')->nullable();
            $table->json('answer_data')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_question_responses');
    }
};
