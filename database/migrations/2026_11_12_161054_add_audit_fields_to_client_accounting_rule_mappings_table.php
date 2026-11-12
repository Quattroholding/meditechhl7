<?php

use App\Models\User;
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
        Schema::table('client_accounting_rule_mappings', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_accounting_rule_mappings', function (Blueprint $table) {
            $table->dropForeignIdFor(User::class, 'created_by');
            $table->dropForeignIdFor(User::class, 'updated_by');
            $table->dropColumn(['created_by', 'updated_by']);
        });
    }
};
