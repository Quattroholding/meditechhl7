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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('validation_status', ['pending', 'approved', 'rejected'])->nullable()->after('active');
            $table->timestamp('validated_at')->nullable()->after('validation_status');
            $table->unsignedBigInteger('validated_by')->nullable()->after('validated_at');
            $table->text('rejection_reason')->nullable()->after('validated_by');

            $table->foreign('validated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['validated_by']);
            $table->dropColumn(['validation_status', 'validated_at', 'validated_by', 'rejection_reason']);
        });
    }
};
