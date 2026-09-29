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
        Schema::table('document_uploads', function (Blueprint $table) {
            $table->string('invoice_number')->nullable()->after('document_type');
            $table->date('invoice_date')->nullable()->after('invoice_number');
            $table->decimal('subtotal', 12, 2)->nullable()->after('invoice_date');
            $table->decimal('total_tax', 12, 2)->nullable()->after('subtotal');
            $table->decimal('total', 12, 2)->nullable()->after('total_tax');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_uploads', function (Blueprint $table) {
            $table->dropColumn(['invoice_number', 'invoice_date', 'subtotal', 'total_tax', 'total']);
        });
    }
};
