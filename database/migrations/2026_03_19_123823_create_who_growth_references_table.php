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
        Schema::create('who_growth_references', function (Blueprint $table) {
            $table->id();
            $table->enum('gender', ['male', 'female']);
            $table->integer('age_months')->comment('Age in months (0-60)');
            $table->enum('measurement_type', [
                'weight_for_age',
                'height_for_age',
                'bmi_for_age',
                'head_circumference_for_age',
            ]);
            $table->decimal('l', 10, 6)->comment('Box-Cox power transformation (L)');
            $table->decimal('m', 10, 4)->comment('Median (M)');
            $table->decimal('s', 10, 6)->comment('Coefficient of variation (S)');
            $table->decimal('p3', 10, 2)->nullable()->comment('3rd percentile');
            $table->decimal('p10', 10, 2)->nullable()->comment('10th percentile');
            $table->decimal('p25', 10, 2)->nullable()->comment('25th percentile');
            $table->decimal('p50', 10, 2)->nullable()->comment('50th percentile (median)');
            $table->decimal('p75', 10, 2)->nullable()->comment('75th percentile');
            $table->decimal('p90', 10, 2)->nullable()->comment('90th percentile');
            $table->decimal('p97', 10, 2)->nullable()->comment('97th percentile');
            $table->timestamps();

            $table->unique(['gender', 'age_months', 'measurement_type']);
            $table->index(['gender', 'measurement_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('who_growth_references');
    }
};
