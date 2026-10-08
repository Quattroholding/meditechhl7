<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Add if columns don't exist
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
                $table->enum('credit_status', ['active', 'blocked'])->nullable()->after('credit_days');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumnIfExists(['allows_credit', 'credit_limit', 'credit_days', 'credit_status']);
        });
    }
};
