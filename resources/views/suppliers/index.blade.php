@extends('layouts.app')

@section('content')
<div class="space-y-6" data-auto-animate>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-display text-slate-900 dark:text-white flex items-center gap-2.5">
                <i class="fas fa-truck-moving text-cyan-600 dark:text-cyan-400"></i> Matting &amp; Accessories Suppliers
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">Direct manufacturers, importers, and distributor contacts</p>
        </div>
        <button type="button" onclick="openSupplierModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-semibold text-xs shadow-lg shadow-cyan-500/20 transition-all">
            <i class="fas fa-plus"></i>
            <span>Add New Supplier</span>
        </button>
    </div>

    <!-- Search Form -->
    <div class="glass-card rounded-2xl p-4 border border-slate-200 dark:border-slate-800">
        <form method="GET" action="{{ route('suppliers.index') }}" class="flex gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500">
                    <i class="fas fa-search text-xs"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search supplier name, contact person, or phone number..."
                    class="w-full pl-9 pr-4 py-2 bg-white dark:bg-dark-900 border border-slate-300 dark:border-slate-700/80 rounded-xl text-slate-900 dark:text-white text-xs placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
            </div>
            <button type="submit" class="px-5 py-2 bg-slate-100 dark:bg-dark-700 hover:bg-slate-200 dark:hover:bg-dark-600 text-cyan-600 dark:text-cyan-400 font-semibold text-xs rounded-xl border border-slate-300 dark:border-slate-700 transition-colors">
                Search
            </button>
        </form>
    </div>

    <!-- Suppliers Table -->
    <div class="glass-card rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-dark-850 text-slate-600 dark:text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-200 dark:border-slate-800">
                        <th class="py-3 px-4">Supplier Name</th>
                        <th class="py-3 px-4">Contact Person</th>
                        <th class="py-3 px-4">Phone / Contact</th>
                        <th class="py-3 px-4">Address</th>
                        <th class="py-3 px-4">Notes</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60" data-auto-animate>
                    @forelse($suppliers as $s)
                    <tr class="hover:bg-slate-50 dark:hover:bg-dark-800/40 transition-colors">
                        <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white text-sm">
                            {{ $s->supplier_name }}
                        </td>
                        <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300">
                            {{ $s->contact_person ?: '-' }}
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-mono text-cyan-600 dark:text-cyan-400 font-semibold">{{ $s->contact_number ?: '-' }}</div>
                            @if($s->email)
                            <div class="text-[11px] text-slate-500">{{ $s->email }}</div>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400 max-w-xs truncate">
                            {{ $s->address ?: '-' }}
                        </td>
                        <td class="py-3.5 px-4 text-slate-500 text-[11px] max-w-xs truncate">
                            {{ $s->notes ?: '-' }}
                        </td>
                        <td class="py-3.5 px-4 text-right space-x-1">
                            <button type="button" onclick="editSupplier({{ json_encode($s) }})" class="p-1.5 rounded-lg bg-slate-100 dark:bg-dark-800 hover:bg-slate-200 dark:hover:bg-dark-700 text-slate-700 dark:text-slate-300 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors border border-slate-300 dark:border-slate-700">
                                <i class="fas fa-pen-to-square"></i>
                            </button>
                            <form method="POST" action="{{ route('suppliers.destroy', $s->id) }}" class="inline" onsubmit="return confirm('Delete supplier {{ addslashes($s->supplier_name) }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg bg-slate-100 dark:bg-dark-800 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 transition-colors border border-slate-300 dark:border-slate-700">
                                    <i class="fas fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">No suppliers found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $suppliers->links() }}
        </div>
    </div>
</div>

<!-- Modal: Add / Edit Supplier -->
<div id="supplierModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="w-full max-w-md bg-white dark:bg-[#0c1222] border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <h3 id="modalTitle" class="text-base font-bold font-display text-slate-900 dark:text-white">Add Supplier</h3>
            <button type="button" onclick="closeSupplierModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-xl leading-none">&times;</button>
        </div>

        <form id="supplierForm" method="POST" action="{{ route('suppliers.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="space-y-3 text-xs">
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 font-semibold mb-1">Company / Supplier Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="supplier_name" id="supName" required placeholder="e.g. Direct Auto Matting Imports"
                        class="w-full py-2 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs focus:ring-1 focus:ring-cyan-500">
                </div>

                <div>
                    <label class="block text-slate-700 dark:text-slate-300 font-semibold mb-1">Contact Person</label>
                    <input type="text" name="contact_person" id="supPerson" placeholder="e.g. Carlos Tan"
                        class="w-full py-2 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs focus:ring-1 focus:ring-cyan-500">
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 font-semibold mb-1">Phone Number</label>
                        <input type="text" name="contact_number" id="supPhone" placeholder="0917-000-0000"
                            class="w-full py-2 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs focus:ring-1 focus:ring-cyan-500">
                    </div>
                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 font-semibold mb-1">Email</label>
                        <input type="email" name="email" id="supEmail" placeholder="supplier@domain.ph"
                            class="w-full py-2 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs focus:ring-1 focus:ring-cyan-500">
                    </div>
                </div>

                <div>
                    <label class="block text-slate-700 dark:text-slate-300 font-semibold mb-1">Warehouse / Office Address</label>
                    <input type="text" name="address" id="supAddress" placeholder="City, Metro Manila / Province"
                        class="w-full py-2 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs focus:ring-1 focus:ring-cyan-500">
                </div>

                <div>
                    <label class="block text-slate-700 dark:text-slate-300 font-semibold mb-1">Notes / Supplied Products</label>
                    <textarea name="notes" id="supNotes" rows="2" placeholder="Supplies 3-row deep dish, coils, clips..."
                        class="w-full py-2 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs focus:ring-1 focus:ring-cyan-500"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 mt-4 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeSupplierModal()" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-dark-800 hover:bg-slate-200 dark:hover:bg-dark-700 text-slate-700 dark:text-slate-300 font-semibold text-xs">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-md">
                    Save Supplier
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openSupplierModal() {
    document.getElementById('modalTitle').innerText = 'Add New Supplier';
    document.getElementById('supplierForm').action = "{{ route('suppliers.store') }}";
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('supplierForm').reset();

    const modal = document.getElementById('supplierModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function editSupplier(s) {
    document.getElementById('modalTitle').innerText = 'Edit Supplier: ' + s.supplier_name;
    document.getElementById('supplierForm').action = '/suppliers/' + s.id;
    document.getElementById('formMethod').value = 'PUT';

    document.getElementById('supName').value = s.supplier_name || '';
    document.getElementById('supPerson').value = s.contact_person || '';
    document.getElementById('supPhone').value = s.contact_number || '';
    document.getElementById('supEmail').value = s.email || '';
    document.getElementById('supAddress').value = s.address || '';
    document.getElementById('supNotes').value = s.notes || '';

    const modal = document.getElementById('supplierModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeSupplierModal() {
    const modal = document.getElementById('supplierModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endsection
