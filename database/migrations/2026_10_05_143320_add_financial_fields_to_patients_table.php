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
        Schema::table('patients', function (Blueprint $table) {
            // Agregar campos de crédito
            if (! Schema::hasColumn('patients', 'allows_credit')) {
                $table->boolean('allows_credit')->default(false)->after('email');
            }

            if (! Schema::hasColumn('patients', 'credit_limit')) {
                $table->decimal('credit_limit', 15, 2)->nullable()->after('allows_credit');
            }

            if (! Schema::hasColumn('patients', 'credit_days')) {
                $table->integer('credit_days')->nullable()->after('credit_limit');
            }

            if (! Schema::hasColumn('patients', 'credit_status')) {
                $table->enum('credit_status', ['active', 'blocked', 'suspended'])->nullable()->after('credit_days');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            if (Schema::hasColumn('patients', 'allows_credit')) {
                $table->dropColumn('allows_credit');
            }

            if (Schema::hasColumn('patients', 'credit_limit')) {
                $table->dropColumn('credit_limit');
            }

            if (Schema::hasColumn('patients', 'credit_days')) {
                $table->dropColumn('credit_days');
            }

            if (Schema::hasColumn('patients', 'credit_status')) {
                $table->dropColumn('credit_status');
            }
        });
    }
};
