@extends('layouts.app')

@section('content')
<div class="space-y-6" data-auto-animate>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold font-display text-slate-900 dark:text-white flex items-center gap-3">
                <i class="fas fa-hard-drive text-cyan-500"></i> Database Backup Module
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
        <!-- Settings Form (Manual vs Auto) -->
        <div class="lg:col-span-1 glass-card rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
            <div class="border-b border-slate-200 dark:border-slate-800 pb-3">
                <h3 class="text-base font-bold font-display text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-gear text-cyan-500"></i> Backup Configuration
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Configure manual vs automatic schedules</p>
            </div>

            <form method="POST" action="{{ route('backup.settings') }}" class="space-y-4 text-sm">
                @csrf

                <!-- Mode Selection: Manual vs Automatic -->
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
                    <p class="text-[11px] text-slate-400 mt-1">Automatically cleans up old backups to save storage on your E: drive.</p>
                </div>

                <!-- Storage Path Display -->
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">Storage Folder Path</label>
                    <input type="text" name="storage_path" value="{{ $settings->storage_path }}"
                        class="w-full py-2 px-3 bg-slate-100 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl font-mono text-xs text-slate-700 dark:text-slate-300 focus:ring-1 focus:ring-cyan-500">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 dark:bg-dark-700 hover:bg-cyan-600 dark:hover:bg-cyan-600 text-white font-bold text-xs transition-colors">
                        Save Configuration
                    </button>
                </div>
            </form>
        </div>

        <!-- Backups List Table (2 Columns) -->
        <div class="lg:col-span-2 glass-card rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold font-display text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-clock-rotate-left text-cyan-500"></i> Available Backups on E: Drive
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ count($files) }} backup archive(s) found</p>
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
                                <td class="py-3.5 px-4 text-right space-x-2">
                                    <a href="{{ route('backup.download', $file['name']) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-cyan-50 dark:bg-cyan-500/15 text-cyan-600 dark:text-cyan-400 hover:bg-cyan-100 dark:hover:bg-cyan-500/25 border border-cyan-300 dark:border-cyan-500/30 text-xs font-bold transition-colors" title="Download to PC">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    <form method="POST" action="{{ route('backup.destroy', $file['name']) }}" class="inline" onsubmit="return confirm('Permanently delete backup {{ $file['name'] }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-slate-100 dark:bg-dark-800 hover:bg-rose-100 dark:hover:bg-rose-950/40 text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 text-xs transition-colors" title="Delete Archive">
                                            <i class="fas fa-trash-can"></i>
                                        </button>
                                    </form>
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

<script>
function toggleAutoSchedule(isAuto) {
    const group = document.getElementById('frequencyGroup');
    if (isAuto) {
        group.classList.remove('opacity-40', 'pointer-events-none');
    } else {
        group.classList.add('opacity-40', 'pointer-events-none');
    }
}
</script>
@endsection
