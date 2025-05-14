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
        Schema::create('present_illnesse_types', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['location', 'duration', 'timing','severity'])->nullable();
            $table->string('value',50);
            $table->string('value_esp',50);
            $table->integer('order');
            $table->string('path_icon',25)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('present_illnesse_types');
    }
};
