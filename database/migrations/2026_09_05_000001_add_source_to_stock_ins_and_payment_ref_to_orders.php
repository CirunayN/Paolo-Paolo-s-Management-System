<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_ins', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_ins', 'source')) {
                $table->string('source')->nullable()->after('supplier_id');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_ins', function (Blueprint $table) {
            if (Schema::hasColumn('stock_ins', 'source')) {
                $table->dropColumn('source');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'payment_reference')) {
                $table->dropColumn('payment_reference');
            }
        });
    }
};
