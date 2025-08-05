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
        Schema::create('user_widget_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('dashboard_type'); // 'doctor', 'assistant', 'patient'
            $table->string('widget_name');
            $table->string('widget_description');
            $table->boolean('is_visible')->default(true);
            $table->integer('order_position')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'dashboard_type', 'widget_name'], 'user_widget_prefs_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_widget_preferences');
    }
};
