@extends('layouts.app')

@section('content')
<div class="space-y-6" data-auto-animate>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold font-display text-white flex items-center gap-2.5">
                <i class="fas fa-truck-loading text-cyan-400"></i> Stock-In Receiving History
            </h2>
        </div>
        <a href="{{ route('stock-in.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-semibold text-xs shadow-lg shadow-cyan-500/20 transition-all">
            <i class="fas fa-plus"></i>
            <span>Receive New Stock Delivery</span>
        </a>
    </div>

    <!-- Stock-In Log Table -->
    <div class="glass-card rounded-2xl border border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-dark-850 text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-800">
                        <th class="py-3 px-4">PO # / Ref</th>
                        <th class="py-3 px-4">Source / Wholesaler</th>
                        <th class="py-3 px-4">Delivery Date</th>
                        <th class="py-3 px-4 text-center">Items Received</th>
                        <th class="py-3 px-4 text-right">Total Cost</th>
                        <th class="py-3 px-4">Received By</th>
                        <th class="py-3 px-4 text-center">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60" data-auto-animate>
                    @forelse($stockIns as $stk)
                    <tr class="hover:bg-dark-800/40 transition-colors">
                        <td class="py-3.5 px-4 font-mono font-bold text-cyan-400">
                            {{ $stk->reference_no }}
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-white">
                            {{ $stk->source ?: ($stk->supplier ? $stk->supplier->supplier_name : 'General Wholesaler / Spot') }}
                        </td>
                        <td class="py-3.5 px-4 text-slate-300 font-mono text-[11px]">
                            {{ $stk->received_date->format('M d, Y') }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-bold text-slate-300">
                            {{ $stk->items->count() }} items
                        </td>
                        <td class="py-3.5 px-4 text-right font-extrabold font-display text-white text-sm">
                            ₱ {{ number_format($stk->total_cost, 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-slate-400 text-[11px]">
                            {{ $stk->user ? $stk->user->name : 'Staff' }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <a href="{{ route('stock-in.show', $stk->id) }}" class="p-1.5 rounded-lg bg-dark-800 hover:bg-dark-700 text-slate-300 hover:text-cyan-400 text-xs transition-colors">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-slate-500">
                            No inward deliveries recorded yet. Click "Receive New Stock Delivery" to add.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-800">
            {{ $stockIns->links() }}
        </div>
    </div>
</div>
@endsection
