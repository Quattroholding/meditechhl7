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
        Schema::table('medication_requests', function (Blueprint $table) {
            // Change duration from integer to string
            $table->string('duration', 100)->nullable()->change();

            // Add additional_indications column
            $table->text('additional_indications')->nullable()->after('note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medication_requests', function (Blueprint $table) {
            // Revert duration back to integer
            $table->integer('duration')->nullable()->change();

            // Drop additional_indications column
            $table->dropColumn('additional_indications');
        });
    }
};
