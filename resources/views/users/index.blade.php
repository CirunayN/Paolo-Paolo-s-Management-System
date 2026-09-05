@extends('layouts.app')

@section('content')
<div class="space-y-6" data-auto-animate>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-display text-white flex items-center gap-2.5">
                <i class="fas fa-user-shield text-cyan-400"></i> Staff &amp; User Management
            </h2>
        </div>
        <button type="button" onclick="openUserModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-semibold text-xs shadow-lg shadow-cyan-500/20 transition-all">
            <i class="fas fa-user-plus"></i>
            <span>Create New User</span>
        </button>
    </div>

    <!-- Users Table -->
    <div class="glass-card rounded-2xl border border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-dark-850 text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-800">
                        <th class="py-3 px-4">Staff Member</th>
                        <th class="py-3 px-4">Username</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Role</th>
                        <th class="py-3 px-4">Phone</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60" data-auto-animate>
                    @foreach($users as $u)
                    <tr class="hover:bg-dark-800/40 transition-colors">
                        <td class="py-3.5 px-4 font-bold text-white flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-cyan-600 to-blue-600 flex items-center justify-center font-bold text-white text-xs">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <span>{{ $u->name }}</span>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-cyan-400 font-semibold">
                            {{ $u->username ?: '-' }}
                        </td>
                        <td class="py-3.5 px-4 text-slate-300">
                            {{ $u->email }}
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $u->role === 'admin' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'bg-blue-500/20 text-blue-300 border border-blue-500/30' }}">
                                {{ $u->role }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-400">
                            {{ $u->phone ?: '-' }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $u->is_active ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                                {{ $u->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right space-x-1">
                            <button type="button" onclick="editUser({{ json_encode($u) }})" class="p-1.5 rounded-lg bg-dark-800 hover:bg-dark-700 text-slate-300 hover:text-cyan-400 text-xs transition-colors">
                                <i class="fas fa-pen-to-square"></i>
                            </button>
                            @if(auth()->id() !== $u->id)
                            <form method="POST" action="{{ route('users.destroy', $u->id) }}" class="inline" onsubmit="return confirm('Delete account for {{ addslashes($u->name) }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg bg-dark-800 hover:bg-rose-950/40 text-slate-400 hover:text-rose-400 text-xs transition-colors">
                                    <i class="fas fa-trash-can"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-800">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- Modal: Add / Edit User -->
<div id="userModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="w-full max-w-md bg-[#0c1222] border border-slate-700 rounded-2xl p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 id="userModalTitle" class="text-base font-bold font-display text-white">Create Staff Account</h3>
            <button type="button" onclick="closeUserModal()" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <form id="userForm" method="POST" action="{{ route('users.store') }}">
            @csrf
            <input type="hidden" name="_method" id="userFormMethod" value="POST">

            <div class="space-y-3 text-xs">
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Full Name <span class="text-rose-400">*</span></label>
                    <input type="text" name="name" id="uName" required placeholder="e.g. Maria Santos"
                        class="w-full py-2 px-3 bg-dark-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-cyan-500">
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Username <span class="text-rose-400">*</span></label>
                        <input type="text" name="username" id="uUsername" required placeholder="e.g. cashier2"
                            class="w-full py-2 px-3 bg-dark-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-cyan-500">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Role <span class="text-rose-400">*</span></label>
                        <select name="role" id="uRole" required class="w-full py-2 px-3 bg-dark-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-cyan-500">
                            <option value="cashier">Cashier</option>
                            <option value="admin">Admin / Owner</option>
                            <option value="staff">Staff / Stock</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Email Address <span class="text-rose-400">*</span></label>
                    <input type="email" name="email" id="uEmail" required placeholder="staff@paolopaolo.com"
                        class="w-full py-2 px-3 bg-dark-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-cyan-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Phone Number</label>
                    <input type="text" name="phone" id="uPhone" placeholder="0917-000-0000"
                        class="w-full py-2 px-3 bg-dark-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-cyan-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">
                        Password <span id="pwdNotice" class="text-slate-500 text-[10px] font-normal">(Leave blank to keep current)</span>
                    </label>
                    <input type="password" name="password" id="uPassword" placeholder="••••••••"
                        class="w-full py-2 px-3 bg-dark-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-cyan-500">
                </div>

                <div id="activeToggleContainer" class="flex items-center pt-1 hidden">
                    <label class="flex items-center text-xs text-slate-300 cursor-pointer">
                        <input type="checkbox" name="is_active" id="uIsActive" value="1" class="w-4 h-4 rounded bg-dark-900 border-slate-700 text-cyan-500 mr-2">
                        <span>Account is active</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 mt-4 border-t border-slate-800">
                <button type="button" onclick="closeUserModal()" class="px-4 py-2 rounded-xl bg-dark-800 text-slate-300 font-semibold text-xs">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs">
                    Save User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openUserModal() {
    document.getElementById('userModalTitle').innerText = 'Create Staff Account';
    document.getElementById('userForm').action = "{{ route('users.store') }}";
    document.getElementById('userFormMethod').value = 'POST';
    document.getElementById('userForm').reset();
    document.getElementById('uPassword').required = true;
    document.getElementById('pwdNotice').innerText = '(Minimum 6 characters)';
    document.getElementById('activeToggleContainer').classList.add('hidden');

    const modal = document.getElementById('userModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function editUser(u) {
    document.getElementById('userModalTitle').innerText = 'Edit User: ' + u.name;
    document.getElementById('userForm').action = '/users/' + u.id;
    document.getElementById('userFormMethod').value = 'PUT';

    document.getElementById('uName').value = u.name || '';
    document.getElementById('uUsername').value = u.username || '';
    document.getElementById('uEmail').value = u.email || '';
    document.getElementById('uRole').value = u.role || 'cashier';
    document.getElementById('uPhone').value = u.phone || '';
    document.getElementById('uPassword').value = '';
    document.getElementById('uPassword').required = false;
    document.getElementById('pwdNotice').innerText = '(Leave blank to keep unchanged)';

    document.getElementById('uIsActive').checked = !!u.is_active;
    document.getElementById('activeToggleContainer').classList.remove('hidden');

    const modal = document.getElementById('userModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeUserModal() {
    const modal = document.getElementById('userModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endsection
