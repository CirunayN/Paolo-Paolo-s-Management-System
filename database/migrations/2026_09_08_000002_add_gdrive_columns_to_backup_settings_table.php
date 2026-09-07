<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('backup_settings', function (Blueprint $table) {
            $table->boolean('gdrive_enabled')->default(false)->after('storage_path');
            $table->string('gdrive_folder_id')->nullable()->after('gdrive_enabled');
            $table->text('gdrive_credentials_path')->nullable()->after('gdrive_folder_id');
            $table->boolean('gdrive_auto_upload')->default(false)->after('gdrive_credentials_path');
            $table->timestamp('last_gdrive_upload_at')->nullable()->after('gdrive_auto_upload');
        });
    }

    public function down(): void
    {
        Schema::table('backup_settings', function (Blueprint $table) {
            $table->dropColumn([
                'gdrive_enabled',
                'gdrive_folder_id',
                'gdrive_credentials_path',
                'gdrive_auto_upload',
                'last_gdrive_upload_at',
            ]);
        });
    }
};
