@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold font-display text-white">Receive Stock Delivery (Stock-In)</h2>
            <p class="text-xs text-slate-400">Log inward shipments from suppliers. <span class="text-cyan-400 font-semibold">Automatically updates Inventory levels.</span></p>
        </div>
        <a href="{{ route('stock-in.index') }}" class="text-xs text-slate-400 hover:text-white font-medium">
            &larr; Back to History
        </a>
    </div>

    <!-- Banner Notice on Auto Inventory Sync -->
    <div class="p-4 rounded-xl bg-cyan-950/50 border border-cyan-500/30 text-cyan-300 text-xs flex items-center gap-3">
        <i class="fas fa-arrows-rotate text-cyan-400 text-lg animate-spin" style="animation-duration: 4s;"></i>
        <div>
            <strong>Automatic Inventory Synchronization:</strong>
            Submitting this delivery will automatically increase the quantities on hand in the <strong>Inventory Module</strong> and log an entry into the stock audit trail.
        </div>
    </div>

    <form method="POST" action="{{ route('stock-in.store') }}" class="glass-card rounded-2xl p-6 border border-slate-800 space-y-6" id="stockInForm">
        @csrf

        <!-- Delivery Header Info -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Reference No -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                    Reference / PO # <span class="text-rose-400">*</span>
                </label>
                <input type="text" name="reference_no" value="{{ old('reference_no', $autoRef) }}" required
                    class="w-full py-2 px-3 bg-dark-900 border border-slate-700/80 rounded-xl text-white text-xs uppercase font-mono focus:ring-1 focus:ring-cyan-500">
            </div>

            <!-- Source / Wholesaler (No specific supplier required) -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                    Source / Wholesaler (Optional)
                </label>
                <input type="text" name="source" list="sourceSuggestions" value="{{ old('source') }}" placeholder="e.g. Banawe Importer, Shopee Bulk, Spot Trader"
                    class="w-full py-2 px-3 bg-dark-900 border border-slate-700/80 rounded-xl text-white text-xs focus:ring-1 focus:ring-cyan-500">
                <datalist id="sourceSuggestions">
                    <option value="Banawe / Manila Wholesaler">
                    <option value="Binondo Importer">
                    <option value="Direct Container Cargo">
                    <option value="Shopee / Online Bulk">
                    <option value="Davao Spot Trader">
                    <option value="Walk-in Consignment">
                    @foreach($recentSources as $rs)
                    <option value="{{ $rs }}">
                    @endforeach
                </datalist>
            </div>

            <!-- Received Date -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                    Delivery Date <span class="text-rose-400">*</span>
                </label>
                <input type="date" name="received_date" value="{{ old('received_date', date('Y-m-d')) }}" required
                    class="w-full py-2 px-3 bg-dark-900 border border-slate-700/80 rounded-xl text-white text-xs focus:ring-1 focus:ring-cyan-500">
            </div>
        </div>

        <!-- Items Table -->
        <div class="space-y-3">
            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-300">Items Received in this Shipment</span>
                <button type="button" onclick="addItemRow()" class="px-3 py-1 rounded-lg bg-cyan-500/20 text-cyan-300 hover:bg-cyan-500 hover:text-white border border-cyan-500/40 text-xs font-semibold transition-all">
                    <i class="fas fa-plus mr-1"></i> Add Another Item
                </button>
            </div>

            <div id="itemsContainer" class="space-y-2.5" data-auto-animate>
                <!-- Initial Row -->
                <div class="item-row p-3 rounded-xl bg-dark-850/80 border border-slate-700/80 grid grid-cols-1 sm:grid-cols-12 gap-2.5 items-center text-xs">
                    <div class="sm:col-span-6">
                        <label class="block text-[10px] text-slate-400 mb-1">Product</label>
                        <select name="items[0][product_id]" required onchange="onProductSelect(this, 0)"
                            class="w-full py-1.5 px-2 bg-dark-900 border border-slate-700 rounded-lg text-white text-xs focus:ring-1 focus:ring-cyan-500">
                            <option value="">Select product to restock...</option>
                            @foreach($products as $p)
                            <option value="{{ $p->id }}" data-cost="{{ $p->cost_price }}" data-stock="{{ (float)($p->inventory->quantity_on_hand ?? 0) }}" data-unit="{{ $p->unit_of_measure }}">
                                {{ $p->name }} [{{ $p->product_code }}] (Current Stock: {{ (float)($p->inventory->quantity_on_hand ?? 0) }} {{ $p->unit_of_measure }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-[10px] text-slate-400 mb-1">Qty Received</label>
                        <input type="number" name="items[0][quantity_received]" value="10" min="1" step="1" required oninput="calculateRowTotal()"
                            class="w-full py-1.5 px-2 bg-dark-900 border border-slate-700 rounded-lg text-white text-xs row-qty focus:ring-1 focus:ring-cyan-500">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-[10px] text-slate-400 mb-1">Cost / Unit (₱)</label>
                        <input type="number" name="items[0][cost_per_unit]" value="0" min="0" step="0.01" required oninput="calculateRowTotal()"
                            class="w-full py-1.5 px-2 bg-dark-900 border border-slate-700 rounded-lg text-white text-xs row-cost focus:ring-1 focus:ring-cyan-500">
                    </div>

                    <div class="sm:col-span-2 flex items-center justify-between sm:justify-end gap-2 pt-4 sm:pt-0">
                        <div class="text-right">
                            <span class="text-[10px] text-slate-500 block">Subtotal</span>
                            <span class="font-bold text-cyan-400 row-subtotal">₱ 0.00</span>
                        </div>
                        <button type="button" onclick="removeItemRow(this)" class="text-slate-500 hover:text-rose-400 p-1 transition-colors">
                            <i class="fas fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Calculation & Notes -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-slate-800">
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Notes / Delivery Remarks</label>
                <textarea name="notes" rows="2" placeholder="Driver name, courier tracking, box condition..."
                    class="w-full py-2 px-3 bg-dark-900 border border-slate-700 rounded-xl text-white text-xs focus:ring-1 focus:ring-cyan-500"></textarea>
            </div>
            <div class="flex flex-col justify-end text-right">
                <span class="text-xs text-slate-400">Total Inward Shipment Cost</span>
                <span id="grandShipmentTotal" class="text-2xl font-black font-display text-white">₱ 0.00</span>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
            <a href="{{ route('stock-in.index') }}" class="px-5 py-2.5 rounded-xl bg-dark-800 hover:bg-dark-700 text-slate-300 font-semibold text-xs transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold text-xs shadow-lg shadow-cyan-500/20 transition-all">
                <i class="fas fa-check-circle mr-1.5"></i> Save &amp; Update Inventory Stocks
            </button>
        </div>
    </form>
</div>

<!-- Product options template string -->
<div id="productOptionsHtml" class="hidden">
    <option value="">Select product to restock...</option>
    @foreach($products as $p)
    <option value="{{ $p->id }}" data-cost="{{ $p->cost_price }}" data-stock="{{ (float)($p->inventory->quantity_on_hand ?? 0) }}" data-unit="{{ $p->unit_of_measure }}">
        {{ $p->name }} [{{ $p->product_code }}] (Current Stock: {{ (float)($p->inventory->quantity_on_hand ?? 0) }} {{ $p->unit_of_measure }})
    </option>
    @endforeach
</div>

<script>
let rowIndex = 1;

function addItemRow() {
    const container = document.getElementById('itemsContainer');
    const options = document.getElementById('productOptionsHtml').innerHTML;

    const row = document.createElement('div');
    row.className = 'item-row p-3 rounded-xl bg-dark-850/80 border border-slate-700/80 grid grid-cols-1 sm:grid-cols-12 gap-2.5 items-center text-xs';
    row.innerHTML = `
        <div class="sm:col-span-6">
            <label class="block text-[10px] text-slate-400 mb-1">Product</label>
            <select name="items[${rowIndex}][product_id]" required onchange="onProductSelect(this, ${rowIndex})"
                class="w-full py-1.5 px-2 bg-dark-900 border border-slate-700 rounded-lg text-white text-xs focus:ring-1 focus:ring-cyan-500">
                ${options}
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="block text-[10px] text-slate-400 mb-1">Qty Received</label>
            <input type="number" name="items[${rowIndex}][quantity_received]" value="10" min="1" step="1" required oninput="calculateRowTotal()"
                class="w-full py-1.5 px-2 bg-dark-900 border border-slate-700 rounded-lg text-white text-xs row-qty focus:ring-1 focus:ring-cyan-500">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-[10px] text-slate-400 mb-1">Cost / Unit (₱)</label>
            <input type="number" name="items[${rowIndex}][cost_per_unit]" value="0" min="0" step="0.01" required oninput="calculateRowTotal()"
                class="w-full py-1.5 px-2 bg-dark-900 border border-slate-700 rounded-lg text-white text-xs row-cost focus:ring-1 focus:ring-cyan-500">
        </div>
        <div class="sm:col-span-2 flex items-center justify-between sm:justify-end gap-2 pt-4 sm:pt-0">
            <div class="text-right">
                <span class="text-[10px] text-slate-500 block">Subtotal</span>
                <span class="font-bold text-cyan-400 row-subtotal">₱ 0.00</span>
            </div>
            <button type="button" onclick="removeItemRow(this)" class="text-slate-500 hover:text-rose-400 p-1 transition-colors">
                <i class="fas fa-trash-can"></i>
            </button>
        </div>
    `;

    container.appendChild(row);
    rowIndex++;
    calculateRowTotal();
}

function removeItemRow(btn) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length > 1) {
        btn.closest('.item-row').remove();
        calculateRowTotal();
    } else {
        alert('Shipment must contain at least 1 product item.');
    }
}

function onProductSelect(selectEl, index) {
    const opt = selectEl.options[selectEl.selectedIndex];
    const cost = opt.getAttribute('data-cost') || 0;
    const row = selectEl.closest('.item-row');
    const costInput = row.querySelector('.row-cost');
    costInput.value = parseFloat(cost).toFixed(2);
    calculateRowTotal();
}

function calculateRowTotal() {
    let grandTotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.row-qty').value) || 0;
        const cost = parseFloat(row.querySelector('.row-cost').value) || 0;
        const subtotal = qty * cost;
        row.querySelector('.row-subtotal').innerText = '₱ ' + subtotal.toLocaleString('en-US', {minimumFractionDigits: 2});
        grandTotal += subtotal;
    });

    document.getElementById('grandShipmentTotal').innerText = '₱ ' + grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2});
}

document.addEventListener('DOMContentLoaded', () => {
    calculateRowTotal();
});
</script>
@endsection
