@extends('layouts.app')

@section('content')
<div class="space-y-6" data-auto-animate>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold font-display text-slate-900 dark:text-white flex items-center gap-3">
                <i class="fas fa-hard-drive text-cyan-500"></i> Database Backup &amp; Restore
            </h2>
        </div>
        <form method="POST" action="{{ route('backup.create') }}">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2.5 px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold text-sm shadow-lg shadow-cyan-500/20 transition-all transform hover:-translate-y-0.5">
                <i class="fas fa-download text-base"></i>
                <span>Backup Database Now</span>
            </button>
        </form>
    </div>

    <!-- Configuration Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Settings Form + External Restore -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Settings Form (Manual vs Auto) -->
            <div class="glass-card rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                <div class="border-b border-slate-200 dark:border-slate-800 pb-3">
                    <h3 class="text-base font-bold font-display text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-gear text-cyan-500"></i> Backup Configuration
                    </h3>
                </div>

                <form method="POST" action="{{ route('backup.settings') }}" enctype="multipart/form-data" class="space-y-4 text-sm">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Backup Mode</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center justify-center p-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-dark-800 cursor-pointer hover:border-cyan-500 transition-colors text-center has-[:checked]:border-cyan-500 has-[:checked]:bg-cyan-500/10">
                                <input type="radio" name="backup_mode" value="automatic" {{ $settings->backup_mode === 'automatic' ? 'checked' : '' }} class="hidden" onchange="toggleAutoSchedule(true)">
                                <div class="text-center">
                                    <i class="fas fa-arrows-rotate text-cyan-500 text-lg mb-1 block"></i>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 text-xs">Automatic</span>
                                </div>
                            </label>
                            <label class="flex items-center justify-center p-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-dark-800 cursor-pointer hover:border-cyan-500 transition-colors text-center has-[:checked]:border-cyan-500 has-[:checked]:bg-cyan-500/10">
                                <input type="radio" name="backup_mode" value="manual" {{ $settings->backup_mode === 'manual' ? 'checked' : '' }} class="hidden" onchange="toggleAutoSchedule(false)">
                                <div class="text-center">
                                    <i class="fas fa-hand-pointer text-amber-500 text-lg mb-1 block"></i>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 text-xs">Manual Only</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Frequency Selection -->
                    <div id="frequencyGroup">
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">Auto-Backup Frequency</label>
                        <select name="frequency" class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm focus:ring-1 focus:ring-cyan-500">
                            <option value="1_day" {{ $settings->frequency === '1_day' ? 'selected' : '' }}>Every 1 Day (Daily)</option>
                            <option value="1_week" {{ $settings->frequency === '1_week' ? 'selected' : '' }}>Every 1 Week (Weekly)</option>
                            <option value="1_month" {{ $settings->frequency === '1_month' ? 'selected' : '' }}>Every 1 Month (Monthly)</option>
                        </select>
                    </div>

                    <!-- Retention Rule Selection -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">Auto-Cleanup Retention Rule</label>
                        <select name="retention" class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm focus:ring-1 focus:ring-cyan-500">
                            <option value="1_week" {{ $settings->retention === '1_week' ? 'selected' : '' }}>Delete backups older than 1 Week</option>
                            <option value="1_month" {{ $settings->retention === '1_month' ? 'selected' : '' }}>Delete backups older than 1 Month</option>
                            <option value="1_year" {{ $settings->retention === '1_year' ? 'selected' : '' }}>Delete backups older than 1 Year</option>
                            <option value="keep_all" {{ $settings->retention === 'keep_all' ? 'selected' : '' }}>Keep All (No auto-delete)</option>
                        </select>
                    </div>

                    <!-- Storage Path Display & Explorer Action -->
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">Storage Folder Path</label>
                        <div class="flex items-center gap-1.5">
                            <input type="text" name="storage_path" id="storagePathInput" value="{{ $settings->storage_path }}"
                                class="flex-1 py-2 px-3 bg-slate-100 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl font-mono text-xs text-slate-800 dark:text-slate-200 focus:ring-1 focus:ring-cyan-500">
                            <button type="button" id="openExplorerBtn" onclick="openFolderInExplorer()" 
                                class="px-3 py-2 rounded-xl bg-slate-200 dark:bg-dark-800 hover:bg-cyan-500 hover:text-white dark:hover:bg-cyan-600 text-slate-700 dark:text-slate-300 text-xs font-bold transition-all flex items-center gap-1.5 whitespace-nowrap shadow-sm"
                                title="Open this folder in Windows File Explorer">
                                <i class="fas fa-arrow-up-right-from-square text-cyan-500"></i>
                                <span>Open in Explorer</span>
                            </button>
                        </div>
                    </div>

                    <!-- GOOGLE DRIVE ONLINE CLOUD BACKUP SECTION -->
                    <div class="pt-3 border-t border-slate-200 dark:border-slate-800 space-y-3.5">
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="gdrive_enabled" value="1" {{ $settings->gdrive_enabled ? 'checked' : '' }}
                                    class="w-4 h-4 rounded text-cyan-600 focus:ring-cyan-500 border-slate-300 dark:border-slate-700">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-200 flex items-center gap-1.5">
                                    <i class="fab fa-google-drive text-emerald-500 text-sm"></i>
                                    Google Drive Cloud Backup
                                </span>
                            </label>
                            @php
                                $hasCreds = \App\Services\GoogleDriveBackupService::isConfigured($settings);
                            @endphp
                            @if($hasCreds)
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                                <i class="fas fa-check-circle mr-0.5"></i> Connected
                            </span>
                            @else
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-200 dark:bg-dark-800 text-slate-500">
                                Not Configured
                            </span>
                            @endif
                        </div>

                        <!-- Service Account Credentials JSON -->
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">
                                Google Service Account Key (JSON)
                            </label>
                            <input type="file" name="gdrive_credentials_file" accept=".json"
                                class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 dark:file:bg-cyan-950/40 dark:file:text-cyan-300 hover:file:bg-cyan-100 cursor-pointer">
                            <p class="text-[10px] text-slate-400 mt-1">
                                Upload your Google Cloud service account <code class="font-mono text-cyan-600">credentials.json</code> file.
                            </p>
                        </div>

                        <!-- Target Folder ID -->
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">
                                Google Drive Folder ID <span class="text-slate-400 font-normal">(optional)</span>
                            </label>
                            <input type="text" name="gdrive_folder_id" value="{{ $settings->gdrive_folder_id }}" placeholder="e.g. 1AbCdEfGhIjKlMnOpQrStUvWxYz"
                                class="w-full py-2 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-mono text-slate-900 dark:text-white focus:ring-1 focus:ring-cyan-500">
                            <p class="text-[10px] text-slate-400 mt-1">
                                Found in your Google Drive URL: <code class="font-mono text-cyan-600">drive.google.com/drive/folders/<strong>[FOLDER_ID]</strong></code>. Share this folder with your service account email as Editor.
                            </p>
                        </div>

                        <!-- Auto Upload on Creation -->
                        <div class="pt-1">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="gdrive_auto_upload" value="1" {{ $settings->gdrive_auto_upload ? 'checked' : '' }}
                                    class="w-4 h-4 rounded text-cyan-600 focus:ring-cyan-500 border-slate-300 dark:border-slate-700">
                                <span class="text-xs font-medium text-slate-700 dark:text-slate-300">
                                    Automatically upload every new backup to Google Drive
                                </span>
                            </label>
                        </div>

                        @if($settings->last_gdrive_upload_at)
                        <div class="text-[11px] text-slate-400">
                            <i class="fas fa-cloud-arrow-up text-emerald-500 mr-1"></i> Last Google Drive upload: <strong>{{ $settings->last_gdrive_upload_at->format('M d, Y h:i A') }}</strong>
                        </div>
                        @endif
                    </div>

                    <div class="pt-2 flex items-center gap-2">
                        <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-slate-900 dark:bg-dark-700 hover:bg-cyan-600 dark:hover:bg-cyan-600 text-white font-bold text-xs transition-colors">
                            Save Configuration
                        </button>
                    </div>
                </form>

                <!-- Test Google Drive Connection Button -->
                <form method="POST" action="{{ route('backup.gdrive-test') }}" class="pt-1">
                    @csrf
                    <button type="submit" class="w-full py-2 px-3 rounded-xl border border-emerald-500/40 hover:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 font-bold text-xs flex items-center justify-center gap-1.5 transition-colors">
                        <i class="fab fa-google-drive"></i>
                        <span>Test Google Drive Connection</span>
                    </button>
                </form>
            </div>

            <!-- External SQL Restore Card -->
            <div class="glass-card rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                <div class="border-b border-slate-200 dark:border-slate-800 pb-3">
                    <h3 class="text-base font-bold font-display text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-file-arrow-up text-amber-500"></i> Restore External Backup
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Import and restore a .sql or .gz database archive</p>
                </div>

                <form method="POST" action="{{ route('backup.restore-upload') }}" enctype="multipart/form-data" id="uploadRestoreForm" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Select SQL File</label>
                        <input type="file" name="backup_file" id="uploadBackupFileInput" accept=".sql,.gz" required
                            class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-500/10 file:text-amber-600 dark:file:text-amber-400 hover:file:bg-amber-500/20 file:cursor-pointer border border-slate-300 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-dark-900 p-1">
                        <p class="text-[11px] text-slate-400 mt-1">Accepts .sql or .sql.gz (up to 100MB)</p>
                    </div>

                    <button type="button" onclick="openUploadRestoreModal()" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-white font-bold text-xs shadow-md shadow-amber-500/20 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-rotate-left"></i>
                        <span>Restore From Uploaded File</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Column: Backups List Table (2 Columns) -->
        <div class="lg:col-span-2 glass-card rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold font-display text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-clock-rotate-left text-cyan-500"></i> Available Backups on E: Drive
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ count($files) }} backup found</p>
                    </div>
                    @if($settings->last_backup_at)
                    <span class="text-xs text-slate-500 dark:text-slate-400">
                        Last backup: <strong class="text-cyan-600 dark:text-cyan-400">{{ $settings->last_backup_at->diffForHumans() }}</strong>
                    </span>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-dark-850 text-slate-500 dark:text-slate-400 uppercase tracking-wider text-xs font-bold border-b border-slate-200 dark:border-slate-800">
                                <th class="py-3 px-4">Backup File Name</th>
                                <th class="py-3 px-4">Date &amp; Time Created</th>
                                <th class="py-3 px-4 text-center">File Size</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60" data-auto-animate>
                            @forelse($files as $file)
                            <tr class="hover:bg-slate-50 dark:hover:bg-dark-800/40 transition-colors">
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-900 dark:text-white text-xs">
                                    <i class="fas fa-file-code text-cyan-500 mr-2 text-sm"></i>{{ $file['name'] }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300 text-xs">
                                    {{ $file['created_at']->format('M d, Y h:i:s A') }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-semibold text-slate-700 dark:text-slate-300 text-xs">
                                    {{ $file['size'] }}
                                </td>
                                <td class="py-3.5 px-4 text-right space-x-1.5 whitespace-nowrap">
                                    <a href="{{ route('backup.download', $file['name']) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-cyan-50 dark:bg-cyan-500/15 text-cyan-600 dark:text-cyan-400 hover:bg-cyan-100 dark:hover:bg-cyan-500/25 border border-cyan-300 dark:border-cyan-500/30 text-xs font-bold transition-colors" title="Download to PC">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    <form method="POST" action="{{ route('backup.gdrive-upload', $file['name']) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-500/25 border border-emerald-300 dark:border-emerald-500/30 text-xs font-bold transition-colors" title="Upload this backup to Google Drive">
                                            <i class="fab fa-google-drive"></i> Drive
                                        </button>
                                    </form>
                                    <button type="button" 
                                        onclick="openRestoreModal('{{ $file['name'] }}', '{{ route('backup.restore', $file['name']) }}')"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-500/25 border border-amber-300 dark:border-amber-500/30 text-xs font-bold transition-colors" 
                                        title="Restore Database to this state">
                                        <i class="fas fa-rotate-left"></i> Restore
                                    <button type="button" 
                                        onclick="openRemoveBackupModal('{{ $file['name'] }}', '{{ route('backup.destroy', $file['name']) }}')"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-dark-800 hover:bg-rose-100 dark:hover:bg-rose-950/40 text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 text-xs font-bold transition-colors" 
                                        title="Remove Backup File">
                                        <i class="fas fa-trash-can text-xs"></i>
                                        <span>Remove</span>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-slate-500">
                                    <i class="fas fa-database text-4xl mb-3 text-slate-400 dark:text-slate-600 block"></i>
                                    <p class="font-semibold text-slate-700 dark:text-slate-300">No backup files yet in {{ $backupDir }}</p>
                                    <p class="text-xs text-slate-500 mt-1">Click "Backup Database Now" above to generate your first backup.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Drive Status Banner -->
            <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-dark-850 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                <span class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    E: Drive Connected &amp; Operational
                </span>
                <span>Direct Local Path: <code class="font-mono text-cyan-600 dark:text-cyan-400">{{ $backupDir }}</code></span>
            </div>
        </div>
    </div>
</div>

<!-- Restore Confirmation Modal -->
<div id="restoreModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="w-full max-w-lg bg-white dark:bg-[#0c1222] border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-2xl space-y-5">
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <h3 class="text-base font-bold font-display text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-triangle-exclamation text-amber-500"></i> Database Restore Confirmation
            </h3>
            <button type="button" onclick="closeRestoreModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg">&times;</button>
        </div>

        <!-- Warning Card -->
        <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/25 text-amber-800 dark:text-amber-300 text-xs space-y-2">
            <div class="font-bold flex items-center gap-1.5 text-amber-700 dark:text-amber-200">
                <i class="fas fa-shield-halved text-amber-500"></i> Automatic Pre-Restore Safety Snapshot
            </div>
            <p>
                Restoring will replace current database tables and data with the selected backup snapshot.
                An automatic backup of your <strong>current state</strong> will be saved to your backup drive prior to restoring so you can easily revert if needed.
            </p>
        </div>

        <!-- Target File Display -->
        <div class="bg-slate-50 dark:bg-dark-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3">
            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-1">Target Backup File:</span>
            <div id="restoreTargetFilename" class="font-mono text-xs font-bold text-cyan-600 dark:text-cyan-400 break-all"></div>
        </div>

        <!-- Confirmation Form -->
        <form id="restoreModalForm" method="POST" action="" onsubmit="return handleRestoreSubmit(this)">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        Please type <span class="font-mono text-rose-500 font-bold select-all">RESTORE</span> below to unlock confirmation:
                    </label>
                    <input type="text" id="restoreConfirmInput" autocomplete="off" placeholder="Type RESTORE"
                        oninput="validateRestoreConfirm(this.value)"
                        class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm font-mono tracking-widest uppercase focus:ring-1 focus:ring-amber-500">
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" onclick="closeRestoreModal()" 
                        class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-100 dark:hover:bg-dark-800 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" id="restoreSubmitBtn" disabled
                        class="px-5 py-2.5 rounded-xl bg-amber-500 text-white text-xs font-bold shadow-md opacity-40 cursor-not-allowed transition-all flex items-center gap-2">
                        <i class="fas fa-rotate-left" id="restoreBtnIcon"></i>
                        <span id="restoreBtnText">Restore Database Now</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let currentRestoreMode = 'table'; // 'table' or 'upload'

function toggleAutoSchedule(isAuto) {
    const group = document.getElementById('frequencyGroup');
    if (isAuto) {
        group.classList.remove('opacity-40', 'pointer-events-none');
    } else {
        group.classList.add('opacity-40', 'pointer-events-none');
    }
}

function openRestoreModal(filename, restoreUrl) {
    currentRestoreMode = 'table';
    document.getElementById('restoreTargetFilename').textContent = filename;
    document.getElementById('restoreModalForm').action = restoreUrl;
    resetRestoreModalState();
    
    const modal = document.getElementById('restoreModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    setTimeout(() => {
        document.getElementById('restoreConfirmInput').focus();
    }, 100);
}

function openUploadRestoreModal() {
    const fileInput = document.getElementById('uploadBackupFileInput');
    if (!fileInput.files || fileInput.files.length === 0) {
        alert('Please choose a .sql or .gz backup file from your device first.');
        fileInput.focus();
        return;
    }
    
    currentRestoreMode = 'upload';
    const filename = fileInput.files[0].name;
    document.getElementById('restoreTargetFilename').textContent = filename + ' (External Upload)';
    resetRestoreModalState();
    
    const modal = document.getElementById('restoreModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    setTimeout(() => {
        document.getElementById('restoreConfirmInput').focus();
    }, 100);
}

function closeRestoreModal() {
    const modal = document.getElementById('restoreModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    resetRestoreModalState();
}

function resetRestoreModalState() {
    const input = document.getElementById('restoreConfirmInput');
    input.value = '';
    const btn = document.getElementById('restoreSubmitBtn');
    btn.disabled = true;
    btn.classList.add('opacity-40', 'cursor-not-allowed');
    btn.classList.remove('hover:bg-amber-600');
    
    const text = document.getElementById('restoreBtnText');
    text.textContent = 'Restore Database Now';
    const icon = document.getElementById('restoreBtnIcon');
    icon.className = 'fas fa-rotate-left';
}

function validateRestoreConfirm(value) {
    const btn = document.getElementById('restoreSubmitBtn');
    if (value.trim().toUpperCase() === 'RESTORE') {
        btn.disabled = false;
        btn.classList.remove('opacity-40', 'cursor-not-allowed');
        btn.classList.add('hover:bg-amber-600');
    } else {
        btn.disabled = true;
        btn.classList.add('opacity-40', 'cursor-not-allowed');
        btn.classList.remove('hover:bg-amber-600');
    }
}

function handleRestoreSubmit(form) {
    const btn = document.getElementById('restoreSubmitBtn');
    btn.disabled = true;
    btn.classList.add('opacity-70', 'cursor-wait');
    
    const text = document.getElementById('restoreBtnText');
    text.textContent = 'Restoring Database... Please wait.';
    const icon = document.getElementById('restoreBtnIcon');
    icon.className = 'fas fa-spinner fa-spin';

    if (currentRestoreMode === 'upload') {
        document.getElementById('uploadRestoreForm').submit();
        return false;
    }

    return true;
}

function openFolderInExplorer() {
    const input = document.getElementById('storagePathInput');
    const btn = document.getElementById('openExplorerBtn');
    const originalHtml = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin text-cyan-500"></i><span>Opening...</span>';

    fetch("{{ route('backup.open-explorer') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json"
        },
        body: JSON.stringify({
            path: input.value
        })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert(data.message || 'Could not open folder in Windows File Explorer.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Could not open folder in Windows File Explorer.');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}

function openRemoveBackupModal(filename, actionUrl) {
    document.getElementById('removeBackupFilename').innerText = filename;
    document.getElementById('removeBackupForm').action = actionUrl;
    const modal = document.getElementById('removeBackupModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeRemoveBackupModal() {
    const modal = document.getElementById('removeBackupModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>

<!-- CONFIRMATION MODAL: Remove Database Backup File -->
<div id="removeBackupModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="w-full max-w-md bg-white dark:bg-[#0c1222] border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-7 shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-200">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xl shrink-0 border border-rose-500/20">
                <i class="fas fa-trash-can"></i>
            </div>
            <div class="space-y-1">
                <h3 class="text-lg font-bold font-display text-slate-900 dark:text-white">Remove Database Backup</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Permanently delete backup file from disk
                </p>
            </div>
        </div>

        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-dark-900 border border-slate-200 dark:border-slate-800 space-y-1">
            <div class="text-[11px] uppercase tracking-wider text-slate-400 font-bold">Target Backup File:</div>
            <div class="text-xs font-mono font-bold text-rose-500 break-all" id="removeBackupFilename">filename.sql</div>
        </div>

        <div class="p-3 rounded-xl bg-rose-50/70 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/50 text-[11px] text-rose-700 dark:text-rose-300 flex items-start gap-2">
            <i class="fas fa-triangle-exclamation text-rose-500 mt-0.5"></i>
            <span><strong>Permanent Action:</strong> This backup archive will be permanently deleted from your backup drive. This action cannot be undone.</span>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="button" onclick="closeRemoveBackupModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-dark-800 text-slate-700 dark:text-slate-300 font-semibold text-xs hover:bg-slate-200 dark:hover:bg-dark-700 transition-colors cursor-pointer">
                Cancel
            </button>
            <form id="removeBackupForm" method="POST" action="" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-500 to-red-600 hover:from-rose-600 hover:to-red-700 text-white font-bold text-xs shadow-lg shadow-rose-500/20 transition-all cursor-pointer flex items-center gap-1.5">
                    <i class="fas fa-trash-can"></i>
                    <span>Remove Backup</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
