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
            $table->timestamp('notification_sent_at')->nullable()->after('consulting_room_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medication_requests', function (Blueprint $table) {
            $table->dropColumn('notification_sent_at');
        });
    }
};
