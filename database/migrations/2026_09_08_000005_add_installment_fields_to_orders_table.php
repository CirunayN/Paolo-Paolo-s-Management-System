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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_type')->default('Full')->after('payment_method'); // Full, Installment
            $table->decimal('amount_paid', 10, 2)->default(0.00)->after('amount_tendered');
            $table->decimal('balance_due', 10, 2)->default(0.00)->after('amount_paid');
            $table->date('due_date')->nullable()->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'amount_paid', 'balance_due', 'due_date']);
        });
    }
};
