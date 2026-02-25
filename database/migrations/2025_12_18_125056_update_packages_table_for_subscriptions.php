<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            if (! Schema::hasColumn('packages', 'slug')) {
                $table->string('slug')->nullable()->after('name');
            }

            if (! Schema::hasColumn('packages', 'base_price')) {
                $table->decimal('base_price', 10, 3)->default(0)->after('show_price');
            }

            if (! Schema::hasColumn('packages', 'max_doctors_included')) {
                $table->integer('max_doctors_included')->default(1)->after('base_price');
            }

            if (! Schema::hasColumn('packages', 'price_per_extra_doctor')) {
                $table->decimal('price_per_extra_doctor', 10, 3)->default(0)->after('max_doctors_included');
            }

            if (! Schema::hasColumn('packages', 'features')) {
                $table->json('features')->nullable()->after('price_per_extra_doctor');
            }

            if (! Schema::hasColumn('packages', 'billing_period')) {
                $table->string('billing_period')->default('monthly')->after('is_active');
            }

            if (! Schema::hasColumn('packages', 'billing_period_days')) {
                $table->integer('billing_period_days')->default(30)->after('billing_period');
            }

            if (! Schema::hasColumn('packages', 'agent_available')) {
                $table->boolean('agent_available')->default(false)->after('billing_period_days');
            }

            if (! Schema::hasColumn('packages', 'appointments_limit')) {
                $table->integer('appointments_limit')->nullable()->after('agent_available');
            }

            if (! Schema::hasColumn('packages', 'show_on_web')) {
                $table->boolean('show_on_web')->default(0)->after('appointments_limit');
            }

        });

        $packages = DB::table('packages')->get();
        foreach ($packages as $package) {
            $slug = strtolower(str_replace(' ', '-', $package->name));
            DB::table('packages')->where('id', $package->id)->update(['slug' => $slug]);
        }

        Schema::table('packages', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn([
                'slug',
                'base_price',
                'price_per_extra_doctor',
                'features',
                'billing_period',
                'billing_period_days',
                'deleted_at',
                'agent_available',
                'appointments_limit',
            ]);

            if (Schema::hasColumn('packages', 'max_doctors_included')) {
                $table->renameColumn('max_doctors_included', 'max_users');
            }

            if (Schema::hasColumn('packages', 'base_price')) {
                $table->renameColumn('base_price', 'price');
            }

            $table->boolean('show_price')->default(true);
        });
    }
};
