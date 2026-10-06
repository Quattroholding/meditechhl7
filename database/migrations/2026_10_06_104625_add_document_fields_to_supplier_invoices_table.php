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
        Schema::table('supplier_invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('supplier_invoices', 'document_path')) {
                $table->string('document_path')->nullable()->after('notes');
                $table->string('document_filename')->nullable()->after('document_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('supplier_invoices', 'document_path')) {
                $table->dropColumn(['document_path', 'document_filename']);
            }
        });
    }
};
