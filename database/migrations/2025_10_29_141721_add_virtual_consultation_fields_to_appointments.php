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
            $table->enum('consultation_type', ['presencial', 'virtual'])->default('presencial')->after('status');
            $table->string('virtual_room_id')->nullable()->after('consultation_type');
            $table->string('virtual_room_url')->nullable()->after('virtual_room_id');
            $table->timestamp('virtual_session_started_at')->nullable()->after('virtual_room_url');
            $table->timestamp('virtual_session_ended_at')->nullable()->after('virtual_session_started_at');
            $table->json('virtual_session_metadata')->nullable()->after('virtual_session_ended_at')->comment('Recording URL, participants, etc.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn([
                'consultation_type',
                'virtual_room_id',
                'virtual_room_url',
                'virtual_session_started_at',
                'virtual_session_ended_at',
                'virtual_session_metadata',
            ]);
        });
    }
};
