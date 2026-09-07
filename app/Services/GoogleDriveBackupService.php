<?php

namespace App\Services;

use App\Models\BackupSetting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Exception;

class GoogleDriveBackupService
{
    /**
     * Check if Google API Client is available and configured
     */
    public static function isConfigured(?BackupSetting $settings = null): bool
    {
        if (!$settings) {
            $settings = BackupSetting::getSettings();
        }

        if (!$settings->gdrive_enabled) {
            return false;
        }

        $credPath = self::resolveCredentialsPath($settings);
        return !empty($credPath) && File::exists($credPath);
    }

    /**
     * Resolve the absolute path to credentials.json
     */
    public static function resolveCredentialsPath(?BackupSetting $settings = null): ?string
    {
        if (!$settings) {
            $settings = BackupSetting::getSettings();
        }

        if (!empty($settings->gdrive_credentials_path)) {
            $path = $settings->gdrive_credentials_path;
            if (File::exists($path)) {
                return $path;
            }
            if (File::exists(base_path($path))) {
                return base_path($path);
            }
            if (File::exists(storage_path($path))) {
                return storage_path($path);
            }
        }

        // Standard default location in storage/app/google-drive-credentials.json
        $defaultStoragePath = storage_path('app/google-drive-credentials.json');
        if (File::exists($defaultStoragePath)) {
            return $defaultStoragePath;
        }

        return null;
    }

    /**
     * Create an authenticated Google Client
     */
    protected static function createClient(?BackupSetting $settings = null): \Google\Client
    {
        if (!class_exists('\Google\Client')) {
            throw new Exception("Google API Client package is not installed. Please ensure google/apiclient is available.");
        }

        $credPath = self::resolveCredentialsPath($settings);
        if (empty($credPath) || !File::exists($credPath)) {
            throw new Exception("Google Service Account credentials file (JSON) not found. Please upload or specify credentials.json in Backup Settings.");
        }

        $client = new \Google\Client();
        $client->setAuthConfig($credPath);
        $client->addScope(\Google\Service\Drive::DRIVE);
        $client->setAccessType('offline');

        return $client;
    }

    /**
     * Test connection to Google Drive
     */
    public static function testConnection(?BackupSetting $settings = null): array
    {
        try {
            $client = self::createClient($settings);
            $driveService = new \Google\Service\Drive($client);

            $about = $driveService->about->get(['fields' => 'user, storageQuota']);
            $userEmail = $about->getUser() ? $about->getUser()->getEmailAddress() : 'Service Account';

            $folderStatus = 'No folder ID specified (files will upload to root/shared drive)';
            if ($settings && !empty($settings->gdrive_folder_id)) {
                try {
                    $folder = $driveService->files->get($settings->gdrive_folder_id, ['fields' => 'id, name']);
                    $folderStatus = "Connected to folder: '{$folder->getName()}' (ID: {$folder->getId()})";
                } catch (Exception $e) {
                    $folderStatus = "Warning: Specified folder ID '{$settings->gdrive_folder_id}' could not be accessed. Make sure to share the Google Drive folder with '{$userEmail}' as an Editor.";
                }
            }

            return [
                'success' => true,
                'email' => $userEmail,
                'message' => "Successfully authenticated with Google Drive as: {$userEmail}. {$folderStatus}",
            ];
        } catch (Exception $e) {
            Log::error("Google Drive connection test failed: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Google Drive Connection Failed: " . $e->getMessage(),
            ];
        }
    }

    /**
     * Upload a local backup file to Google Drive
     */
    public static function uploadBackup(string $filePath, ?BackupSetting $settings = null): array
    {
        if (!File::exists($filePath)) {
            throw new Exception("Backup file not found at: {$filePath}");
        }

        if (!$settings) {
            $settings = BackupSetting::getSettings();
        }

        $client = self::createClient($settings);
        $driveService = new \Google\Service\Drive($client);

        $fileName = basename($filePath);
        $fileMetadata = new \Google\Service\Drive\DriveFile([
            'name' => $fileName,
            'description' => 'Paolo Paolo Management System Database Backup - Created ' . now()->toDateTimeString(),
        ]);

        if (!empty($settings->gdrive_folder_id)) {
            $fileMetadata->setParents([$settings->gdrive_folder_id]);
        }

        $mimeType = str_ends_with(strtolower($fileName), '.gz') ? 'application/gzip' : 'application/sql';
        $fileContent = File::get($filePath);

        $uploadedFile = $driveService->files->create($fileMetadata, [
            'data' => $fileContent,
            'mimeType' => $mimeType,
            'uploadType' => 'multipart',
            'fields' => 'id, name, webViewLink, size, createdTime',
        ]);

        // Update last upload timestamp
        $settings->last_gdrive_upload_at = now();
        $settings->save();

        Log::info("Backup '{$fileName}' successfully uploaded to Google Drive. File ID: {$uploadedFile->id}");

        return [
            'success' => true,
            'file_id' => $uploadedFile->id,
            'file_name' => $uploadedFile->name,
            'web_link' => $uploadedFile->webViewLink,
            'message' => "Backup '{$fileName}' successfully uploaded to Google Drive!",
        ];
    }
}
