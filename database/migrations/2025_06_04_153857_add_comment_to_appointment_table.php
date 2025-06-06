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
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->references('id')->on('clients')->onDelete('cascade');
            $table->dateTime('original_requested_datetime')->nullable();
            $table->dateTime('practitioner_suggested_datetime')->nullable();
            $table->text('comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('client_id');
            $table->dropColumn('original_requested_datetime');
            $table->dropColumn('practitioner_suggested_datetime');
            $table->dropColumn('comment');
        });
    }
};
