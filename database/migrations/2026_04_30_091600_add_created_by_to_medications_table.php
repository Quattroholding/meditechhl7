<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('manufacturer')->comment('User Created At');
        });

        // Clean up invalid data: set created_by to NULL if the user doesn't exist
        DB::statement('
            UPDATE medications
            SET created_by = NULL
            WHERE created_by IS NOT NULL
            AND created_by NOT IN (SELECT id FROM users)
        ');

        // Now add the foreign key constraint
        Schema::table('medications', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }
};
