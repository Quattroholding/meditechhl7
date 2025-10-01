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
        Schema::table('practitioners', function (Blueprint $table) {
            $table->boolean('prescription_authorization')->default(false)->after('use_signature_digital');
            $table->timestamp('prescription_authorization_date')->nullable()->after('prescription_authorization');
            $table->string('prescription_authorization_ip', 45)->nullable()->after('prescription_authorization_date');
            $table->text('prescription_authorization_terms')->nullable()->after('prescription_authorization_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practitioners', function (Blueprint $table) {
            $table->dropColumn([
                'prescription_authorization',
                'prescription_authorization_date',
                'prescription_authorization_ip',
                'prescription_authorization_terms',
            ]);
        });
    }
};
