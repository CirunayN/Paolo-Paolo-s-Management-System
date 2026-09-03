@extends('layouts.app')

@section('content')
<div class="space-y-6" data-auto-animate>
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold font-display text-white flex items-center gap-2.5">
                <i class="fas fa-clock-rotate-left text-cyan-400"></i> Stock Movement Audit Trail
            </h2>
            <p class="text-xs text-slate-400">Chronological history of all stock inflows, sales deductions, and adjustments</p>
        </div>
        <a href="{{ route('inventory.index') }}" class="text-xs text-slate-400 hover:text-white font-medium">
            &larr; Back to Inventory
        </a>
    </div>

    <!-- Filter Form -->
    <div class="glass-card rounded-2xl p-4 border border-slate-800">
        <form method="GET" action="{{ route('inventory.transactions') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <select name="product_id" class="w-full py-2 px-3 bg-dark-900 border border-slate-700/80 rounded-xl text-white text-xs focus:ring-1 focus:ring-cyan-500">
                    <option value="">All Products</option>
                    @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="type" class="w-full py-2 px-3 bg-dark-900 border border-slate-700/80 rounded-xl text-white text-xs focus:ring-1 focus:ring-cyan-500">
                    <option value="">All Movement Types</option>
                    <option value="stock_in" {{ request('type') == 'stock_in' ? 'selected' : '' }}>Stock-In (Delivery)</option>
                    <option value="pos_sale" {{ request('type') == 'pos_sale' ? 'selected' : '' }}>POS Sale (Deduction)</option>
                    <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Manual Adjustment</option>
                </select>
            </div>
            <div>
                <button type="submit" class="px-5 py-2 bg-dark-700 hover:bg-dark-600 text-cyan-400 font-semibold text-xs rounded-xl border border-slate-700">
                    Filter Trail
                </button>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="glass-card rounded-2xl border border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-dark-850 text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-800">
                        <th class="py-3 px-4">Date &amp; Time</th>
                        <th class="py-3 px-4">Product</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4 text-center">Qty Change</th>
                        <th class="py-3 px-4 text-center">Balance After</th>
                        <th class="py-3 px-4">Reference</th>
                        <th class="py-3 px-4">Staff / Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60" data-auto-animate>
                    @forelse($transactions as $txn)
                    @php
                        $isPlus = $txn->quantity > 0;
                    @endphp
                    <tr class="hover:bg-dark-800/40 transition-colors">
                        <td class="py-3 px-4 text-slate-400 font-mono text-[11px]">
                            {{ $txn->created_at->format('M d, Y h:i A') }}
                        </td>
                        <td class="py-3 px-4 font-semibold text-white">
                            {{ $txn->product ? $txn->product->name : 'N/A' }}
                        </td>
                        <td class="py-3 px-4">
                            @if($txn->type == 'stock_in')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Stock-In</span>
                            @elseif($txn->type == 'pos_sale')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">POS Sale</span>
                            @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">Adjustment</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center font-bold {{ $isPlus ? 'text-emerald-400' : 'text-rose-400' }}">
                            {{ $isPlus ? '+' : '' }}{{ (float)$txn->quantity }}
                        </td>
                        <td class="py-3 px-4 text-center font-bold text-white font-mono">
                            {{ (float)$txn->balance_after }}
                        </td>
                        <td class="py-3 px-4 font-mono text-cyan-400 text-[11px]">
                            {{ $txn->reference_no ?: '-' }}
                        </td>
                        <td class="py-3 px-4 text-slate-400">
                            <div class="text-[11px] text-slate-300">{{ $txn->remarks ?: '-' }}</div>
                            <div class="text-[10px] text-slate-500">By: {{ $txn->user ? $txn->user->name : 'System' }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-500">No stock transactions logged yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-800">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
