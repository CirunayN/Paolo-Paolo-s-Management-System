<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_settings', function (Blueprint $table) {
            $table->id();
            $table->string('backup_mode')->default('automatic'); // manual or automatic
            $table->string('frequency')->default('1_day'); // 1_day, 1_week, 1_month
            $table->string('retention')->default('1_month'); // 1_week, 1_month, 1_year, keep_all
            $table->string('storage_path')->default('E:\\PaoloPaolo_Backups');
            $table->timestamp('last_backup_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_settings');
    }
};
