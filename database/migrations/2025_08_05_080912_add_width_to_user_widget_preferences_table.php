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
        Schema::table('user_widget_preferences', function (Blueprint $table) {
            $table->enum('width', ['col-lg-3', 'col-lg-4', 'col-lg-6', 'col-lg-9', 'col-lg-12'])->default('col-lg-6')->after('order_position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_widget_preferences', function (Blueprint $table) {
            $table->dropColumn('width');
        });
    }
};
