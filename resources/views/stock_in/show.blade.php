@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold font-display text-white">Stock-In Delivery #{{ $stockIn->reference_no }}</h2>
            <p class="text-xs text-slate-400">Received on {{ $stockIn->received_date->format('F d, Y') }}</p>
        </div>
        <a href="{{ route('stock-in.index') }}" class="text-xs text-slate-400 hover:text-white font-medium">
            &larr; Back to History
        </a>
    </div>

    <div class="glass-card rounded-2xl p-6 border border-slate-800 space-y-6">
        <!-- Delivery Meta Info -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 p-4 rounded-xl bg-dark-850/80 border border-slate-700/80 text-xs">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Reference / PO #</span>
                <span class="font-mono font-bold text-cyan-400 text-sm">{{ $stockIn->reference_no }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Source / Wholesaler</span>
                <span class="font-semibold text-white">{{ $stockIn->source ?: ($stockIn->supplier ? $stockIn->supplier->supplier_name : 'General Wholesaler / Spot') }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Received Date</span>
                <span class="font-semibold text-slate-200">{{ $stockIn->received_date->format('M d, Y') }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Received By</span>
                <span class="font-semibold text-slate-200">{{ $stockIn->user ? $stockIn->user->name : 'Staff' }}</span>
            </div>
        </div>

        <!-- Line Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-800">
                        <th class="py-2.5 px-3">Product Name / Code</th>
                        <th class="py-2.5 px-3">Vehicle Model</th>
                        <th class="py-2.5 px-3 text-center">Qty Received</th>
                        <th class="py-2.5 px-3 text-right">Cost Per Unit</th>
                        <th class="py-2.5 px-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @foreach($stockIn->items as $item)
                    <tr>
                        <td class="py-3 px-3">
                            <div class="font-bold text-white">{{ $item->product ? $item->product->name : 'Item' }}</div>
                            <div class="text-[11px] font-mono text-cyan-400">{{ $item->product ? $item->product->product_code : '-' }}</div>
                        </td>
                        <td class="py-3 px-3 text-slate-300">
                            {{ $item->product ? $item->product->vehicle_model : 'Universal' }}
                        </td>
                        <td class="py-3 px-3 text-center font-bold text-white">
                            {{ (float)$item->quantity_received }} {{ $item->product ? $item->product->unit_of_measure : '' }}
                        </td>
                        <td class="py-3 px-3 text-right font-mono text-slate-300">
                            ₱ {{ number_format($item->cost_per_unit, 2) }}
                        </td>
                        <td class="py-3 px-3 text-right font-bold text-cyan-400 font-display text-sm">
                            ₱ {{ number_format($item->subtotal, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t border-slate-700 bg-dark-850/50">
                        <td colspan="4" class="py-3 px-3 font-bold text-white text-right uppercase text-[11px]">Total Shipment Cost:</td>
                        <td class="py-3 px-3 font-black font-display text-white text-base text-right">
                            ₱ {{ number_format($stockIn->total_cost, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($stockIn->notes)
        <div class="p-3.5 rounded-xl bg-dark-900 border border-slate-800 text-xs text-slate-300">
            <strong class="text-slate-400 block mb-1">Remarks:</strong>
            {{ $stockIn->notes }}
        </div>
        @endif
    </div>
</div>
@endsection
