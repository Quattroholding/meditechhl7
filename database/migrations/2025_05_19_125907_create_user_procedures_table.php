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
        Schema::create('user_procedures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade')->comment('User Owner if type USER');
            $table->foreignId('client_id')->nullable()->references('id')->on('clients')->onDelete('cascade')->comment('Client Owner if type CLIENT');
            $table->string('code',10)->unique();
            $table->string('description',250);
            $table->string('cpt_code',10)->nullable();
            $table->decimal('current_price')->default('0.0');
            $table->enum('type',['consulta','injectable','procedimiento','otro'])->comment('Type of procedure');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_procedures');
    }
};
