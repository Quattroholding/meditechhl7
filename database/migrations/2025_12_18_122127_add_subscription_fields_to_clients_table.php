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
        Schema::table('clients', function (Blueprint $table) {
            $table->integer('subscription_billing_day')->nullable()->after('id');
            $table->string('referral_code_used')->nullable()->after('subscription_billing_day');
            $table->foreignId('referred_by_client_id')->nullable()->after('referral_code_used')->constrained('clients')->onDelete('set null');
            $table->text('subscription_notes')->nullable()->after('referred_by_client_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['referred_by_client_id']);
            $table->dropColumn([
                'subscription_billing_day',
                'referral_code_used',
                'referred_by_client_id',
                'subscription_notes',
            ]);
        });
    }
};
