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
        Schema::table('service_catalog', function (Blueprint $table) {
            if (! Schema::hasColumn('service_catalog', 'cost_center_id')) {
                $table->unsignedBigInteger('cost_center_id')->nullable()->after('accounting_account_id');

                $table->foreign('cost_center_id')
                    ->references('id')
                    ->on('cost_centers')
                    ->onDelete('set null');

                $table->index('cost_center_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_catalog', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['cost_center_id']);
            $table->dropIndexIfExists('service_catalog_cost_center_id_index');
            $table->dropColumnIfExists('cost_center_id');
        });
    }
};
