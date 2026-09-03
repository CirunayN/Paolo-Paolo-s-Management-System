@extends('layouts.app')

@section('content')
<div class="space-y-6" data-auto-animate>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-display text-white flex items-center gap-2.5">
                <i class="fas fa-warehouse text-cyan-400"></i> Inventory &amp; Live Stock Control
            </h2>
            <p class="text-xs text-slate-400">Real-time automotive matting inventory on hand and reorder threshold alerts</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('inventory.transactions') }}" class="px-4 py-2 rounded-xl bg-dark-750 hover:bg-dark-700 border border-slate-700 text-slate-300 text-xs font-semibold transition-colors">
                <i class="fas fa-clock-rotate-left mr-1.5 text-cyan-400"></i> Stock Audit Trail
            </a>
            <a href="{{ route('stock-in.create') }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white text-xs font-bold shadow-lg shadow-cyan-500/20 transition-all">
                <i class="fas fa-truck-loading mr-1.5"></i> Receive Delivery
            </a>
        </div>
    </div>

    <!-- Quick Status Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4" data-auto-animate>
        <a href="{{ route('inventory.index') }}" class="glass-card rounded-2xl p-4 border border-slate-800 hover:border-cyan-500/40 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Tracked Items</span>
                <i class="fas fa-boxes text-cyan-400"></i>
            </div>
            <div class="text-2xl font-black font-display text-white mt-2">{{ $totalItemsCount }}</div>
            <div class="text-[11px] text-slate-400 mt-1">All catalog units in stock</div>
        </a>

        <a href="{{ route('inventory.index', ['status' => 'low']) }}" class="glass-card rounded-2xl p-4 border border-slate-800 hover:border-amber-500/40 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Low Stock Warnings</span>
                <i class="fas fa-triangle-exclamation text-amber-400"></i>
            </div>
            <div class="text-2xl font-black font-display text-amber-400 mt-2">{{ $lowStockCount }}</div>
            <div class="text-[11px] text-amber-300/80 mt-1">Below minimum reorder level</div>
        </a>

        <a href="{{ route('inventory.index', ['status' => 'out']) }}" class="glass-card rounded-2xl p-4 border border-slate-800 hover:border-rose-500/40 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Out of Stock</span>
                <i class="fas fa-ban text-rose-400"></i>
            </div>
            <div class="text-2xl font-black font-display text-rose-400 mt-2">{{ $outOfStockCount }}</div>
            <div class="text-[11px] text-rose-300/80 mt-1">Immediate restock needed</div>
        </a>
    </div>

    <!-- Search & Filter Filter Form -->
    <div class="glass-card rounded-2xl p-4 border border-slate-800">
        <form method="GET" action="{{ route('inventory.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                    <i class="fas fa-search text-xs"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search SKU, product or vehicle..."
                    class="w-full pl-9 pr-4 py-2 bg-dark-900 border border-slate-700/80 rounded-xl text-white text-xs placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-cyan-500">
            </div>

            <div>
                <select name="status" class="w-full py-2 px-3 bg-dark-900 border border-slate-700/80 rounded-xl text-white text-xs focus:ring-1 focus:ring-cyan-500">
                    <option value="">All Stock Levels</option>
                    <option value="in" {{ request('status') == 'in' ? 'selected' : '' }}>In Stock Only</option>
                    <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>Low Stock Alerts</option>
                    <option value="out" {{ request('status') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>

            <div class="flex gap-2">
                <select name="category_id" class="w-full py-2 px-3 bg-dark-900 border border-slate-700/80 rounded-xl text-white text-xs focus:ring-1 focus:ring-cyan-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-dark-700 hover:bg-dark-600 text-cyan-400 font-semibold text-xs rounded-xl border border-slate-700 transition-colors">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Inventory Table -->
    <div class="glass-card rounded-2xl border border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-dark-850 text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-800">
                        <th class="py-3 px-4">Product / SKU</th>
                        <th class="py-3 px-4">Vehicle Make &amp; Model</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4 text-center">Stock on Hand</th>
                        <th class="py-3 px-4 text-center">Reorder Level</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Quick Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60" data-auto-animate>
                    @forelse($inventories as $inv)
                    @php
                        $p = $inv->product;
                        $qty = (float)$inv->quantity_on_hand;
                        $reorder = (int)$inv->reorder_level;
                    @endphp
                    <tr class="hover:bg-dark-800/40 transition-colors">
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-white text-sm">{{ $p->name }}</div>
                            <div class="text-[11px] font-mono text-cyan-400">{{ $p->product_code }}</div>
                        </td>
                        <td class="py-3.5 px-4 text-slate-300">
                            <span class="px-2 py-0.5 rounded bg-dark-900 text-slate-300 border border-slate-800 text-[11px]">
                                {{ $p->vehicle_brand }} - {{ $p->vehicle_model ?: 'Universal' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-400">
                            {{ $p->category->name }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="text-base font-black font-display text-white">
                                {{ $qty }}
                            </span>
                            <span class="text-[10px] text-slate-400 block">{{ $p->unit_of_measure }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-center text-slate-400 font-medium">
                            {{ $reorder }} {{ $p->unit_of_measure }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($qty <= 0)
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/40">
                                Out of Stock
                            </span>
                            @elseif($qty <= $reorder)
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">
                                Low Stock
                            </span>
                            @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                                In Stock
                            </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <button type="button" onclick="openAdjustModal({{ $inv->id }}, '{{ addslashes($p->name) }}', {{ $qty }})"
                                class="px-3 py-1.5 rounded-lg bg-dark-800 hover:bg-cyan-500/20 text-slate-300 hover:text-cyan-300 border border-slate-700 text-xs font-medium transition-colors">
                                <i class="fas fa-sliders mr-1"></i> Adjust
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-slate-500">
                            No inventory records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-800">
            {{ $inventories->links() }}
        </div>
    </div>
</div>

<!-- Stock Adjustment Modal -->
<div id="adjustModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="w-full max-w-md bg-[#0c1222] border border-slate-700 rounded-2xl p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-bold font-display text-white">Manual Stock Adjustment</h3>
            <button type="button" onclick="closeAdjustModal()" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <form id="adjustForm" method="POST" action="">
            @csrf
            <div class="space-y-3.5 text-xs">
                <div>
                    <label class="block text-slate-400 mb-1">Product</label>
                    <div id="adjustProductName" class="font-bold text-white text-sm"></div>
                </div>

                <div>
                    <label class="block text-slate-400 mb-1">Current Stock on Hand</label>
                    <div id="adjustCurrentQty" class="font-bold text-cyan-400 text-base"></div>
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">New Adjusted Quantity <span class="text-rose-400">*</span></label>
                    <input type="number" name="new_quantity" id="newQtyInput" min="0" step="1" required
                        class="w-full py-2 px-3 bg-dark-900 border border-slate-700 rounded-xl text-white text-sm font-bold focus:ring-1 focus:ring-cyan-500">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Reason for Adjustment <span class="text-rose-400">*</span></label>
                    <select name="reason" required class="w-full py-2 px-3 bg-dark-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-cyan-500">
                        <option value="Physical Count / Audit">Physical Count / Inventory Audit</option>
                        <option value="Damaged / Scratched Floor Mat">Damaged / Defective Matting</option>
                        <option value="Return from Customer">Customer Return</option>
                        <option value="Display Unit / Demo Set">Used as Shop Display / Demo</option>
                        <option value="Others">Other Adjustment Reason</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 mt-4 border-t border-slate-800">
                <button type="button" onclick="closeAdjustModal()" class="px-4 py-2 rounded-xl bg-dark-800 text-slate-300 font-semibold text-xs">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs">
                    Confirm Adjustment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAdjustModal(invId, prodName, currentQty) {
    document.getElementById('adjustProductName').innerText = prodName;
    document.getElementById('adjustCurrentQty').innerText = currentQty + ' units';
    document.getElementById('newQtyInput').value = currentQty;
    document.getElementById('adjustForm').action = '/inventory/adjust/' + invId;

    const modal = document.getElementById('adjustModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeAdjustModal() {
    const modal = document.getElementById('adjustModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endsection
