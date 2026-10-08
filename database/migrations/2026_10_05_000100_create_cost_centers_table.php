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
        // Only create if table doesn't exist (already created in production)
        if (! Schema::hasTable('cost_centers')) {
            Schema::create('cost_centers', function (Blueprint $table) {
                $table->id();
                $table->uuid()->unique();
                $table->foreignId('client_id')->constrained('clients');
                $table->string('code', 20);
                $table->string('name', 255);
                $table->text('description')->nullable();
                $table->foreignId('branch_id')->nullable()->constrained('branches');
                $table->foreignId('medical_speciality_id')->nullable()->constrained('medical_specialties');
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->foreignId('created_by')->nullable()->constrained('users');
                $table->foreignId('updated_by')->nullable()->constrained('users');
                $table->timestamps();
                $table->softDeletes();
                $table->index(['client_id', 'code']);
                $table->index(['client_id', 'status']);
                $table->unique(['client_id', 'code']);
                $table->unique(['client_id', 'name']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_centers');
    }
};
