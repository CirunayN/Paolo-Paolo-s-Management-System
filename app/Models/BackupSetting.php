<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'backup_mode',
        'frequency',
        'retention',
        'storage_path',
        'last_backup_at',
    ];

    protected $casts = [
        'last_backup_at' => 'datetime',
    ];

    public static function getSettings(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'backup_mode' => 'automatic',
                'frequency' => '1_day',
                'retention' => '1_month',
                'storage_path' => 'E:\\PaoloPaolo_Backups',
            ]
        );
    }
}
