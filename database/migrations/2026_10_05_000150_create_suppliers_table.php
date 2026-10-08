<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Multi-tenancy
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');

            // Identificación
            $table->string('ruc', 20)->comment('RUC sin DV');
            $table->string('dv', 2)->nullable()->comment('Dígito verificador');
            $table->string('legal_name', 255)->comment('Razón social');
            $table->string('commercial_name', 255)->nullable();

            // Contacto
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('contact_person', 255)->nullable();

            // Términos comerciales
            $table->integer('credit_days')->default(0)->comment('Días de crédito');

            // Cuenta contable (CxP)
            $table->foreignId('accounting_account_id')
                ->constrained('accounting_accounts')
                ->onDelete('restrict')
                ->comment('Cuenta de Cuentas por Pagar');

            // Estado
            $table->enum('status', ['active', 'inactive'])->default('active');

            // Auditoría
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->unique(['client_id', 'ruc']);
            $table->index(['client_id', 'status']);
            $table->index('legal_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
