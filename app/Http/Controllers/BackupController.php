<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\BackupSetting;
use PDO;

class BackupController extends Controller
{
    protected string $defaultBackupDir = 'E:\\PaoloPaolo_Backups';

    public function index()
    {
        $settings = BackupSetting::getSettings();
        $backupDir = $this->getBackupDirectory($settings);

        // Ensure directory exists
        if (!File::exists($backupDir)) {
            try {
                File::makeDirectory($backupDir, 0777, true, true);
            } catch (\Exception $e) {
                // If E:\ drive is not available, fallback to storage/backups
                $backupDir = storage_path('app/backups');
                if (!File::exists($backupDir)) {
                    File::makeDirectory($backupDir, 0777, true, true);
                }
            }
        }

        // Auto-run backup if due
        $this->checkAndRunAutoBackup($settings, $backupDir);

        // Auto-cleanup retention
        $this->cleanupOldBackups($settings, $backupDir);

        // Get all backup files
        $files = [];
        if (File::exists($backupDir)) {
            $allFiles = File::files($backupDir);
            foreach ($allFiles as $file) {
                if (in_array(strtolower($file->getExtension()), ['sql', 'gz'])) {
                    $bytes = $file->getSize();
                    $size = $bytes >= 1048576 
                        ? number_format($bytes / 1048576, 2) . ' MB' 
                        : number_format($bytes / 1024, 2) . ' KB';

                    $files[] = [
                        'name' => $file->getFilename(),
                        'path' => $file->getPathname(),
                        'size' => $size,
                        'bytes' => $bytes,
                        'created_at' => Carbon::createFromTimestamp($file->getMTime()),
                    ];
                }
            }

            // Sort newest first
            usort($files, fn($a, $b) => $b['created_at']->timestamp <=> $a['created_at']->timestamp);
        }

        return view('backup.index', compact('settings', 'files', 'backupDir'));
    }

    public function createBackup()
    {
        $settings = BackupSetting::getSettings();
        $backupDir = $this->getBackupDirectory($settings);

        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0777, true, true);
        }

        $filename = 'backup_paolopaolo_' . date('Y-m-d_His') . '.sql';
        $fullPath = rtrim($backupDir, '\\/') . DIRECTORY_SEPARATOR . $filename;

        $success = $this->performDatabaseDump($fullPath);

        if ($success && File::exists($fullPath)) {
            $settings->last_backup_at = now();
            $settings->save();

            $this->cleanupOldBackups($settings, $backupDir);

            return redirect()->route('backup.index')->with('success', "Database backup '{$filename}' created successfully in {$backupDir}!");
        }

        return redirect()->route('backup.index')->with('error', "Failed to create database backup. Please check drive permissions.");
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'backup_mode' => 'required|in:manual,automatic',
            'frequency' => 'required|in:1_day,1_week,1_month',
            'retention' => 'required|in:1_week,1_month,1_year,keep_all',
            'storage_path' => 'nullable|string|max:255',
        ]);

        $settings = BackupSetting::getSettings();
        $settings->update([
            'backup_mode' => $validated['backup_mode'],
            'frequency' => $validated['frequency'],
            'retention' => $validated['retention'],
            'storage_path' => !empty($validated['storage_path']) ? $validated['storage_path'] : $this->defaultBackupDir,
        ]);

        return redirect()->route('backup.index')->with('success', "Backup configuration updated successfully!");
    }

    public function download(string $filename)
    {
        $settings = BackupSetting::getSettings();
        $backupDir = $this->getBackupDirectory($settings);
        $fullPath = rtrim($backupDir, '\\/') . DIRECTORY_SEPARATOR . basename($filename);

        if (File::exists($fullPath)) {
            return response()->download($fullPath);
        }

        return redirect()->route('backup.index')->with('error', 'Backup file not found.');
    }

    public function destroy(string $filename)
    {
        $settings = BackupSetting::getSettings();
        $backupDir = $this->getBackupDirectory($settings);
        $fullPath = rtrim($backupDir, '\\/') . DIRECTORY_SEPARATOR . basename($filename);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
            return redirect()->route('backup.index')->with('success', "Backup file '{$filename}' deleted.");
        }

        return redirect()->route('backup.index')->with('error', 'Backup file not found.');
    }

    protected function getBackupDirectory(BackupSetting $settings): string
    {
        $dir = $settings->storage_path ?: $this->defaultBackupDir;
        if (!File::exists('E:\\') && str_starts_with(strtoupper($dir), 'E:')) {
            return storage_path('app/backups');
        }
        return $dir;
    }

    protected function performDatabaseDump(string $outputPath): bool
    {
        $dbHost = config('database.connections.mysql.host', '127.0.0.1');
        $dbPort = config('database.connections.mysql.port', '3306');
        $dbName = config('database.connections.mysql.database', 'paolo_paolo_management_db');
        $dbUser = config('database.connections.mysql.username', 'root');
        $dbPass = config('database.connections.mysql.password', '');

        // 1. Try mysqldump.exe from XAMPP or system PATH
        $mysqldumpPaths = [
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'mysqldump',
        ];

        foreach ($mysqldumpPaths as $bin) {
            $passPart = !empty($dbPass) ? "-p\"{$dbPass}\"" : "";
            $cmd = "\"{$bin}\" -h {$dbHost} -P {$dbPort} -u {$dbUser} {$passPart} {$dbName} > \"{$outputPath}\" 2>&1";

            @exec($cmd, $output, $returnVar);

            if ($returnVar === 0 && File::exists($outputPath) && File::size($outputPath) > 500) {
                return true;
            }
        }

        // 2. Pure PHP PDO SQL Dumper fallback (100% reliable)
        try {
            $pdo = new PDO("mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $dump = "-- Paolo Paolo D.A Matting & Accessories Database Backup\n";
            $dump .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
            $dump .= "-- Database: {$dbName}\n\n";
            $dump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            foreach ($tables as $table) {
                $createRow = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_NUM);
                $dump .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $dump .= $createRow[1] . ";\n\n";

                $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($rows)) {
                    foreach ($rows as $row) {
                        $cols = array_map(fn($k) => "`$k`", array_keys($row));
                        $vals = array_map(function ($v) use ($pdo) {
                            if (is_null($v)) return "NULL";
                            return $pdo->quote($v);
                        }, array_values($row));

                        $dump .= "INSERT INTO `{$table}` (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $vals) . ");\n";
                    }
                    $dump .= "\n";
                }
            }

            $dump .= "SET FOREIGN_KEY_CHECKS=1;\n";
            File::put($outputPath, $dump);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function checkAndRunAutoBackup(BackupSetting $settings, string $backupDir): void
    {
        if ($settings->backup_mode !== 'automatic') {
            return;
        }

        $last = $settings->last_backup_at;
        $isDue = false;

        if (!$last) {
            $isDue = true;
        } else {
            $now = now();
            switch ($settings->frequency) {
                case '1_day':
                    $isDue = $now->diffInDays($last) >= 1;
                    break;
                case '1_week':
                    $isDue = $now->diffInWeeks($last) >= 1;
                    break;
                case '1_month':
                    $isDue = $now->diffInMonths($last) >= 1;
                    break;
            }
        }

        if ($isDue) {
            $filename = 'autobackup_paolopaolo_' . date('Y-m-d_His') . '.sql';
            $fullPath = rtrim($backupDir, '\\/') . DIRECTORY_SEPARATOR . $filename;
            if ($this->performDatabaseDump($fullPath)) {
                $settings->last_backup_at = now();
                $settings->save();
            }
        }
    }

    protected function cleanupOldBackups(BackupSetting $settings, string $backupDir): void
    {
        if ($settings->retention === 'keep_all' || !File::exists($backupDir)) {
            return;
        }

        $now = now();
        $allFiles = File::files($backupDir);

        foreach ($allFiles as $file) {
            $created = Carbon::createFromTimestamp($file->getMTime());
            $shouldDelete = false;

            switch ($settings->retention) {
                case '1_week':
                    $shouldDelete = $now->diffInDays($created) > 7;
                    break;
                case '1_month':
                    $shouldDelete = $now->diffInDays($created) > 30;
                    break;
                case '1_year':
                    $shouldDelete = $now->diffInDays($created) > 365;
                    break;
            }

            if ($shouldDelete) {
                @File::delete($file->getPathname());
            }
        }
    }
}
