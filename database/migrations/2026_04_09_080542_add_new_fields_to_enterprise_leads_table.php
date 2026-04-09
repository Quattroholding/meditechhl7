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
        Schema::table('enterprise_leads', function (Blueprint $table) {
            $table->unsignedInteger('number_of_doctors')->nullable()->after('company_name');
            $table->enum('agent_preference', ['sami', 'personalized'])->nullable()->after('number_of_doctors');
            $table->unsignedInteger('number_of_branches')->nullable()->after('agent_preference');
            $table->string('current_system', 255)->nullable()->after('number_of_branches');

            // Add indexes for efficient filtering
            $table->index('number_of_doctors');
            $table->index('agent_preference');
            $table->index('number_of_branches');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enterprise_leads', function (Blueprint $table) {
            $table->dropIndex(['number_of_doctors']);
            $table->dropIndex(['agent_preference']);
            $table->dropIndex(['number_of_branches']);

            $table->dropColumn([
                'number_of_doctors',
                'agent_preference',
                'number_of_branches',
                'current_system',
            ]);
        });
    }
};
