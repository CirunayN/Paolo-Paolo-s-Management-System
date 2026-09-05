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
                <div class="flex items-center gap-2">
                    <button type="button" onclick="openQuickAddModal()" class="px-3 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 hover:bg-emerald-500 hover:text-white border border-emerald-500/40 text-xs font-semibold transition-all shadow-sm">
                        <i class="fas fa-magic mr-1"></i> + Quick-Add New Product
                    </button>
                    <button type="button" onclick="addItemRow()" class="px-3 py-1 rounded-lg bg-cyan-500/20 text-cyan-300 hover:bg-cyan-500 hover:text-white border border-cyan-500/40 text-xs font-semibold transition-all">
                        <i class="fas fa-plus mr-1"></i> Add Another Item
                    </button>
                </div>
            </div>

            <div id="itemsContainer" class="space-y-2.5" data-auto-animate>
                <!-- Initial Row -->
                <div class="item-row p-3 rounded-xl bg-dark-850/80 border border-slate-700/80 grid grid-cols-1 sm:grid-cols-12 gap-2.5 items-center text-xs">
                    <div class="sm:col-span-6">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-[10px] text-slate-400">Product</label>
                            <button type="button" onclick="openQuickAddModal(this)" class="text-[10px] font-bold text-cyan-400 hover:text-cyan-300 hover:underline flex items-center gap-1">
                                <i class="fas fa-plus-circle"></i> New Product?
                            </button>
                        </div>
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
            <div class="flex items-center justify-between mb-1">
                <label class="block text-[10px] text-slate-400">Product</label>
                <button type="button" onclick="openQuickAddModal(this)" class="text-[10px] font-bold text-cyan-400 hover:text-cyan-300 hover:underline flex items-center gap-1">
                    <i class="fas fa-plus-circle"></i> New Product?
                </button>
            </div>
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

// Quick-Add Product Modal Logic
let activeRowForQuickProduct = null;

function openQuickAddModal(targetEl = null) {
    if (targetEl) {
        activeRowForQuickProduct = targetEl.closest('.item-row');
    } else {
        const rows = document.querySelectorAll('.item-row');
        activeRowForQuickProduct = rows[rows.length - 1];
    }

    document.getElementById('quickProductForm').reset();
    const errBox = document.getElementById('quickModalError');
    errBox.classList.add('hidden');
    errBox.innerHTML = '';

    const modal = document.getElementById('quickProductModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        const nameInput = document.getElementById('quickName');
        if (nameInput) nameInput.focus();
    }, 150);
}

function closeQuickAddModal() {
    const modal = document.getElementById('quickProductModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function submitQuickProduct(e) {
    e.preventDefault();
    const btn = document.getElementById('quickSubmitBtn');
    const originalContent = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving to Catalog...';

    const errBox = document.getElementById('quickModalError');
    errBox.classList.add('hidden');

    const payload = {
        name: document.getElementById('quickName').value.trim(),
        category_id: document.getElementById('quickCategory').value,
        unit_of_measure: document.getElementById('quickUnit').value,
        vehicle_brand: document.getElementById('quickBrand').value.trim() || 'Universal',
        vehicle_model: document.getElementById('quickModel').value.trim() || 'Universal',
        cost_price: parseFloat(document.getElementById('quickCost').value) || 0,
        unit_price: parseFloat(document.getElementById('quickPrice').value) || 0,
        product_code: document.getElementById('quickCode').value.trim() || null,
    };

    fetch("{{ route('products.quick-store') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json().then(data => ({ status: res.status, data })))
    .then(({ status, data }) => {
        btn.disabled = false;
        btn.innerHTML = originalContent;

        if (status !== 200 || !data.success) {
            let msg = data.message || 'Error registering product.';
            if (data.errors) {
                msg = Object.values(data.errors).flat().join('<br>');
            }
            errBox.innerHTML = msg;
            errBox.classList.remove('hidden');
            return;
        }

        const p = data.product;

        // 1. Build new option tag
        const newOptionHtml = `<option value="${p.id}" data-cost="${p.cost_price}" data-stock="0" data-unit="${p.unit_of_measure}">${p.name} [${p.product_code}] (Current Stock: 0 ${p.unit_of_measure})</option>`;

        // 2. Append to template string #productOptionsHtml for future added rows
        document.getElementById('productOptionsHtml').insertAdjacentHTML('beforeend', newOptionHtml);

        // 3. Append to all existing select dropdowns in current rows
        document.querySelectorAll('.item-row select').forEach(select => {
            select.insertAdjacentHTML('beforeend', newOptionHtml);
        });

        // 4. Pre-select in the active row and auto-populate cost
        if (activeRowForQuickProduct) {
            const selectEl = activeRowForQuickProduct.querySelector('select');
            if (selectEl) {
                selectEl.value = p.id;
                onProductSelect(selectEl);
            }
            const qtyInput = activeRowForQuickProduct.querySelector('.row-qty');
            if (qtyInput) {
                setTimeout(() => {
                    qtyInput.focus();
                    qtyInput.select();
                }, 150);
            }
        }

        closeQuickAddModal();
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = originalContent;
        errBox.innerText = 'Network connection issue. Please check details and try again.';
        errBox.classList.remove('hidden');
    });
}

// Category Quick-Add Modal
function openNewCategoryModal() {
    document.getElementById('newCategoryForm').reset();
    const errBox = document.getElementById('categoryModalError');
    errBox.classList.add('hidden');
    errBox.innerHTML = '';
    const modal = document.getElementById('newCategoryModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => document.getElementById('newCatName').focus(), 150);
}

function closeNewCategoryModal() {
    const modal = document.getElementById('newCategoryModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function submitNewCategory(e) {
    e.preventDefault();
    const btn = document.getElementById('newCatSubmitBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    const errBox = document.getElementById('categoryModalError');
    errBox.classList.add('hidden');

    const nameVal = document.getElementById('newCatName').value.trim();
    const descVal = document.getElementById('newCatDesc').value.trim();

    fetch("{{ route('categories.quick-store') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ name: nameVal, description: descVal })
    })
    .then(res => res.json().then(data => ({ status: res.status, data })))
    .then(({ status, data }) => {
        btn.disabled = false;
        btn.innerHTML = originalText;

        if (status !== 200 || !data.success) {
            let msg = data.message || 'Error creating category.';
            if (data.errors) {
                msg = Object.values(data.errors).flat().join('<br>');
            }
            errBox.innerHTML = msg;
            errBox.classList.remove('hidden');
            return;
        }

        const cat = data.category;
        const select = document.getElementById('quickCategory');
        if (select) {
            const opt = new Option(cat.name, cat.id, true, true);
            select.add(opt);
        }
        closeNewCategoryModal();
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        errBox.innerText = 'Connection error. Please try again.';
        errBox.classList.remove('hidden');
    });
}

// Vehicle Brand Modal for Quick-Add
function openNewBrandModal() {
    document.getElementById('newBrandForm').reset();
    const modal = document.getElementById('newBrandModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => document.getElementById('newBrandInput').focus(), 150);
}

function closeNewBrandModal() {
    const modal = document.getElementById('newBrandModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function submitNewBrand(e) {
    e.preventDefault();
    const name = document.getElementById('newBrandInput').value.trim();
    if (!name) return;

    const select = document.getElementById('quickBrand');
    let exists = false;
    for (let i = 0; i < select.options.length; i++) {
        if (select.options[i].value.toLowerCase() === name.toLowerCase()) {
            select.selectedIndex = i;
            exists = true;
            break;
        }
    }
    if (!exists) {
        const opt = new Option(name, name, true, true);
        select.add(opt);
    }
    closeNewBrandModal();
}

// Unit of Measure Modal for Quick-Add
function openNewUomModal() {
    document.getElementById('newUomForm').reset();
    const modal = document.getElementById('newUomModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => document.getElementById('newUomInput').focus(), 150);
}

function closeNewUomModal() {
    const modal = document.getElementById('newUomModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function submitNewUom(e) {
    e.preventDefault();
    const name = document.getElementById('newUomInput').value.trim();
    if (!name) return;

    const select = document.getElementById('quickUnit');
    let exists = false;
    for (let i = 0; i < select.options.length; i++) {
        if (select.options[i].value.toLowerCase() === name.toLowerCase()) {
            select.selectedIndex = i;
            exists = true;
            break;
        }
    }
    if (!exists) {
        const opt = new Option(name, name, true, true);
        select.add(opt);
    }
    closeNewUomModal();
}

document.addEventListener('DOMContentLoaded', () => {
    calculateRowTotal();
});
</script>

<!-- MODAL: Quick-Add New Product On-The-Fly -->
<div id="quickProductModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="w-full max-w-lg bg-white dark:bg-[#0c1222] border border-slate-200 dark:border-slate-700 rounded-3xl p-6 shadow-2xl space-y-4">
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-base">
                    <i class="fas fa-box-open"></i>
                </span>
                <div>
                    <h3 class="text-base font-bold font-display text-slate-900 dark:text-white">Quick-Register New Product</h3>
                    <p class="text-[11px] text-slate-400">Registers into catalog &amp; automatically selects in current shipment</p>
                </div>
            </div>
            <button type="button" onclick="closeQuickAddModal()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 flex items-center justify-center text-lg">&times;</button>
        </div>

        <!-- Alert Error Banner -->
        <div id="quickModalError" class="hidden p-3 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs"></div>

        <!-- Quick Form -->
        <form id="quickProductForm" onsubmit="submitQuickProduct(event)" class="space-y-3.5 text-xs">
            <!-- Product Name -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Product Name <span class="text-rose-500">*</span></label>
                <input type="text" id="quickName" required placeholder="e.g. Toyota Yaris Cross 2024 Deep Dish Matting"
                    class="w-full py-2 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
            </div>

            <!-- Category & Unit of Measure -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase">Category <span class="text-rose-500">*</span></label>
                        <button type="button" onclick="openNewCategoryModal()" class="text-[10px] font-bold text-cyan-400 hover:underline">
                            + New Category
                        </button>
                    </div>
                    <select id="quickCategory" required
                        class="w-full py-2 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                        <option value="">Select Category...</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase">Unit of Measure</label>
                        <button type="button" onclick="openNewUomModal()" class="text-[10px] font-bold text-cyan-400 hover:underline">
                            + New Unit
                        </button>
                    </div>
                    <select id="quickUnit" required class="w-full py-2 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                        @foreach($unitsOfMeasure as $uom)
                        <option value="{{ $uom }}" {{ $uom === 'Set' ? 'selected' : '' }}>{{ $uom }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Vehicle Brand & Model -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase">Vehicle Make / Brand</label>
                        <button type="button" onclick="openNewBrandModal()" class="text-[10px] font-bold text-cyan-400 hover:underline">
                            + New Brand
                        </button>
                    </div>
                    <select id="quickBrand" class="w-full py-2 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                        <option value="Universal">Universal</option>
                        @foreach($vehicleBrands as $vb)
                        @if($vb !== 'Universal')
                        <option value="{{ $vb }}">{{ $vb }}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Vehicle Model / Year</label>
                    <input type="text" id="quickModel" placeholder="e.g. Yaris Cross 2024, Hilux, Universal"
                        class="w-full py-2 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500">
                </div>
            </div>

            <!-- Cost Price & Selling Price -->
            <div class="grid grid-cols-2 gap-3 p-3 rounded-2xl bg-cyan-50 dark:bg-cyan-950/30 border border-cyan-200 dark:border-cyan-800/50">
                <div>
                    <label class="block font-bold text-cyan-800 dark:text-cyan-300 uppercase mb-1">
                        Supplier Cost (₱) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="quickCost" step="0.01" min="0" required placeholder="0.00"
                        class="w-full py-2 px-3 bg-white dark:bg-dark-900 border border-cyan-300 dark:border-cyan-700 rounded-xl text-slate-900 dark:text-white font-mono font-bold focus:ring-2 focus:ring-cyan-500">
                    <span class="text-[10px] text-slate-400">Auto-fills in this shipment row</span>
                </div>
                <div>
                    <label class="block font-bold text-cyan-800 dark:text-cyan-300 uppercase mb-1">
                        Selling Price (₱) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="quickPrice" step="0.01" min="0" required placeholder="0.00"
                        class="w-full py-2 px-3 bg-white dark:bg-dark-900 border border-cyan-300 dark:border-cyan-700 rounded-xl text-slate-900 dark:text-white font-mono font-bold focus:ring-2 focus:ring-cyan-500">
                    <span class="text-[10px] text-slate-400">POS checkout price</span>
                </div>
            </div>

            <!-- Optional Product Code -->
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1 flex items-center justify-between">
                    <span>Product Code / SKU</span>
                    <span class="text-[10px] lowercase font-normal text-slate-400">(Leave blank to auto-generate)</span>
                </label>
                <input type="text" id="quickCode" placeholder="Auto-generated e.g. MAT-X7812"
                    class="w-full py-2 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono text-xs uppercase focus:ring-2 focus:ring-cyan-500">
            </div>

            <!-- Modal Action Buttons -->
            <div class="pt-2 flex items-center justify-end gap-2.5 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeQuickAddModal()" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-dark-800 text-slate-700 dark:text-slate-300 font-semibold hover:bg-slate-300 transition-colors">
                    Cancel
                </button>
                <button type="submit" id="quickSubmitBtn"
                    class="px-5 py-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-bold shadow-lg shadow-emerald-500/20 transition-all flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>Register &amp; Add to Shipment</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Quick Add Category -->
<div id="newCategoryModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="w-full max-w-md bg-white dark:bg-[#0c1222] border border-slate-200 dark:border-slate-700 rounded-3xl p-6 shadow-2xl space-y-4 animate-fade-in">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-500 flex items-center justify-center text-sm">
                    <i class="fas fa-tags"></i>
                </span>
                <h3 class="text-base font-bold font-display text-slate-900 dark:text-white">Create New Category</h3>
            </div>
            <button type="button" onclick="closeNewCategoryModal()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg flex items-center justify-center">&times;</button>
        </div>

        <div id="categoryModalError" class="hidden p-3 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs"></div>

        <form id="newCategoryForm" onsubmit="submitNewCategory(event)" class="space-y-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Category Name <span class="text-rose-500">*</span></label>
                <input type="text" id="newCatName" required placeholder="e.g. LED Lighting &amp; Bulbs, Dashcams"
                    class="w-full py-2.5 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500 text-sm">
            </div>
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Short Description (Optional)</label>
                <input type="text" id="newCatDesc" placeholder="e.g. Headlights, fog lamps, ambient lighting"
                    class="w-full py-2 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500 text-xs">
            </div>

            <div class="pt-2 flex items-center justify-end gap-2.5 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeNewCategoryModal()" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-dark-800 text-slate-700 dark:text-slate-300 font-semibold hover:bg-slate-300 transition-colors">
                    Cancel
                </button>
                <button type="submit" id="newCatSubmitBtn"
                    class="px-5 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold shadow-lg shadow-cyan-500/20 transition-all">
                    Save Category
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Quick Add Vehicle Brand -->
<div id="newBrandModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="w-full max-w-md bg-white dark:bg-[#0c1222] border border-slate-200 dark:border-slate-700 rounded-3xl p-6 shadow-2xl space-y-4 animate-fade-in">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-500 flex items-center justify-center text-sm">
                    <i class="fas fa-car-side"></i>
                </span>
                <h3 class="text-base font-bold font-display text-slate-900 dark:text-white">Add Vehicle Brand</h3>
            </div>
            <button type="button" onclick="closeNewBrandModal()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg flex items-center justify-center">&times;</button>
        </div>

        <form id="newBrandForm" onsubmit="submitNewBrand(event)" class="space-y-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Brand Name <span class="text-rose-500">*</span></label>
                <input type="text" id="newBrandInput" required placeholder="e.g. VinFast, Zeekr, Omoda, Changan..."
                    class="w-full py-2.5 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500 text-sm">
                <span class="text-[11px] text-slate-400 mt-1 block">Adds this brand to the list and selects it immediately.</span>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2.5 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeNewBrandModal()" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-dark-800 text-slate-700 dark:text-slate-300 font-semibold hover:bg-slate-300 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                    class="px-5 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold shadow-lg shadow-cyan-500/20 transition-all">
                    Add Brand
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Quick Add Unit of Measure -->
<div id="newUomModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="w-full max-w-md bg-white dark:bg-[#0c1222] border border-slate-200 dark:border-slate-700 rounded-3xl p-6 shadow-2xl space-y-4 animate-fade-in">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-500 flex items-center justify-center text-sm">
                    <i class="fas fa-cubes"></i>
                </span>
                <h3 class="text-base font-bold font-display text-slate-900 dark:text-white">Add Unit of Measure</h3>
            </div>
            <button type="button" onclick="closeNewUomModal()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg flex items-center justify-center">&times;</button>
        </div>

        <form id="newUomForm" onsubmit="submitNewUom(event)" class="space-y-4 text-xs">
            <div>
                <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Unit Name <span class="text-rose-500">*</span></label>
                <input type="text" id="newUomInput" required placeholder="e.g. Bundle, Tube, Liter, Box..."
                    class="w-full py-2.5 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-cyan-500 text-sm">
                <span class="text-[11px] text-slate-400 mt-1 block">Adds this unit to the dropdown list and selects it immediately.</span>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2.5 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeNewUomModal()" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-dark-800 text-slate-700 dark:text-slate-300 font-semibold hover:bg-slate-300 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                    class="px-5 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold shadow-lg shadow-cyan-500/20 transition-all">
                    Add Unit
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
