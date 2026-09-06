@extends('layouts.app')

@section('content')
<div class="space-y-6" data-auto-animate>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-display text-slate-900 dark:text-white flex items-center gap-2.5">
                <i class="fas fa-clock-rotate-left text-cyan-500"></i> Stock Movement Audit Trail
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Chronological ledger of stock deliveries, POS sales deductions, and damage/loss adjustments</p>
        </div>
    </div>

    <!-- Inventory Sub-Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-2 overflow-x-auto">
        <a href="{{ route('products.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-dark-800 transition-colors">
            <i class="fas fa-layer-group"></i>
            <span>Catalog &amp; Specs</span>
        </a>
        <a href="{{ route('inventory.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-dark-800 transition-colors">
            <i class="fas fa-clipboard-check"></i>
            <span>Stock Levels &amp; Discrepancies</span>
        </a>
        <a href="{{ route('inventory.transactions') }}" class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold bg-cyan-500 text-white shadow-sm shadow-cyan-500/30">
            <i class="fas fa-clock-rotate-left"></i>
            <span>Stock Audit Trail</span>
        </a>
    </div>

    <!-- Filter Form -->
    <div class="glass-card rounded-2xl p-4 border border-slate-200 dark:border-slate-800">
        <form method="GET" action="{{ route('inventory.transactions') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <select name="product_id" class="w-full py-2 px-3 bg-white dark:bg-dark-900 border border-slate-300 dark:border-slate-700/80 rounded-xl text-slate-900 dark:text-white text-xs focus:ring-1 focus:ring-cyan-500">
                    <option value="">All Products</option>
                    @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="type" class="w-full py-2 px-3 bg-white dark:bg-dark-900 border border-slate-300 dark:border-slate-700/80 rounded-xl text-slate-900 dark:text-white text-xs focus:ring-1 focus:ring-cyan-500">
                    <option value="">All Movement Types</option>
                    <option value="stock_in" {{ request('type') == 'stock_in' ? 'selected' : '' }}>Stock-In (Delivery)</option>
                    <option value="pos_sale" {{ request('type') == 'pos_sale' ? 'selected' : '' }}>POS Sale (Deduction)</option>
                    <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Manual Adjustment</option>
                </select>
            </div>
            <div>
                <button type="submit" class="px-5 py-2 bg-slate-100 dark:bg-dark-700 hover:bg-slate-200 dark:hover:bg-dark-600 text-cyan-600 dark:text-cyan-400 font-semibold text-xs rounded-xl border border-slate-300 dark:border-slate-700 transition-colors">
                    Filter Trail
                </button>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="glass-card rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-dark-850 text-slate-600 dark:text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-200 dark:border-slate-800">
                        <th class="py-3 px-4">Date &amp; Time</th>
                        <th class="py-3 px-4">Product</th>
                        <th class="py-3 px-4">Type</th>
                        <th class="py-3 px-4 text-center">Qty Change</th>
                        <th class="py-3 px-4 text-center">Balance After</th>
                        <th class="py-3 px-4">Reference</th>
                        <th class="py-3 px-4">Staff / Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60" data-auto-animate>
                    @forelse($transactions as $txn)
                    @php
                        $isPlus = $txn->quantity > 0;
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-dark-800/40 transition-colors">
                        <td class="py-3 px-4 text-slate-500 dark:text-slate-400 font-mono text-[11px]">
                            {{ $txn->created_at->format('M d, Y h:i A') }}
                        </td>
                        <td class="py-3 px-4 font-semibold text-slate-900 dark:text-white">
                            {{ $txn->product ? $txn->product->name : 'N/A' }}
                        </td>
                        <td class="py-3 px-4">
                            @if($txn->type == 'stock_in')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 border border-emerald-500/30">Stock-In</span>
                            @elseif($txn->type == 'pos_sale')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/15 text-blue-700 dark:text-blue-300 border border-blue-500/30">POS Sale</span>
                            @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/15 text-amber-700 dark:text-amber-300 border border-amber-500/30">Adjustment</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center font-bold {{ $isPlus ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                            {{ $isPlus ? '+' : '' }}{{ (float)$txn->quantity }}
                        </td>
                        <td class="py-3 px-4 text-center font-bold text-slate-900 dark:text-white font-mono">
                            {{ (float)$txn->balance_after }}
                        </td>
                        <td class="py-3 px-4 font-mono text-cyan-600 dark:text-cyan-400 font-semibold text-[11px]">
                            {{ $txn->reference_no ?: '-' }}
                        </td>
                        <td class="py-3 px-4 text-slate-600 dark:text-slate-400">
                            <div class="text-[11px] text-slate-800 dark:text-slate-300">{{ $txn->remarks ?: '-' }}</div>
                            <div class="text-[10px] text-slate-400 dark:text-slate-500">By: {{ $txn->user ? $txn->user->name : 'System' }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">No stock transactions logged yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
