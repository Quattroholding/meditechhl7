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
        Schema::create('vaccine_types', function (Blueprint $table) {
            $table->id();
            $table->string('cvx_code')->unique()->comment('CDC CVX vaccine code');
            $table->string('vaccine_name_en');
            $table->string('vaccine_name_es');
            $table->string('target_disease')->comment('Disease(s) prevented');
            $table->integer('min_age_months')->default(0)->comment('Minimum age in months');
            $table->integer('max_age_months')->nullable()->comment('Maximum age in months (null = no limit)');
            $table->integer('doses_required')->default(1)->comment('Number of doses in standard series');
            $table->integer('interval_days')->nullable()->comment('Minimum interval between doses in days');
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'display_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccine_types');
    }
};
