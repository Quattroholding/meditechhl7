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
        Schema::create('device_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clinic_id');
            $table->string('device_serial')->nullable();
            $table->string('message_control_id');
            $table->string('message_type')->default('OBS.R01');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['clinic_id', 'message_control_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_messages');
    }
};
