@extends('layouts.app')

@section('content')
<div class="h-[calc(100vh-7.5rem)] flex flex-col lg:flex-row gap-5 -m-2">
    <!-- LEFT COLUMN: Catalog & Products Selection -->
    <div class="flex-1 flex flex-col min-w-0 bg-white dark:bg-[#0c1222] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl overflow-hidden">
        <!-- Top Filters & Search Bar -->
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 space-y-3 bg-slate-50/50 dark:bg-dark-850/50">
            <!-- Search & Count -->
            <div class="flex items-center gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-search text-sm"></i>
                    </div>
                    <input type="text" id="searchInput" placeholder="Search matting SKU, vehicle model, car accessories..."
                        class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition-all">
                </div>
                <div class="text-xs text-slate-600 dark:text-slate-300 font-bold px-3 py-2 bg-slate-200 dark:bg-dark-800 rounded-xl border border-slate-300 dark:border-slate-700 whitespace-nowrap">
                    <span id="productCount" class="font-extrabold text-cyan-600 dark:text-cyan-400 text-sm">{{ count($products) }}</span> Items
                </div>
            </div>

            <!-- Brand Quick-Filter Buttons -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-thin">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mr-1 shrink-0">Brand:</span>
                <button type="button" onclick="filterBrand('all')" data-brand-btn="all"
                    class="brand-filter-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-cyan-600 text-white shadow-sm shrink-0 transition-all">
                    All Brands
                </button>
                @foreach($vehicleBrands as $brand)
                <button type="button" onclick="filterBrand('{{ strtolower($brand) }}')" data-brand-btn="{{ strtolower($brand) }}"
                    class="brand-filter-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-200 dark:bg-dark-800 hover:bg-slate-300 dark:hover:bg-dark-700 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 shrink-0 transition-all">
                    {{ $brand }}
                </button>
                @endforeach
            </div>

            <!-- Category Filter Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-thin">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mr-1 shrink-0">Category:</span>
                <button type="button" onclick="filterCategory('all')" data-cat-btn="all"
                    class="cat-filter-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-cyan-600 text-white shadow-sm shrink-0 transition-all">
                    All Categories
                </button>
                @foreach($categories as $cat)
                <button type="button" onclick="filterCategory('{{ $cat->id }}')" data-cat-btn="{{ $cat->id }}"
                    class="cat-filter-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-200 dark:bg-dark-800 hover:bg-slate-300 dark:hover:bg-dark-700 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 shrink-0 transition-all">
                    {{ $cat->name }}
                </button>
                @endforeach
            </div>
        </div>

        <!-- Products Grid -->
        <div class="flex-1 p-4 overflow-y-auto">
            <div id="productGrid" class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4" data-auto-animate>
                @foreach($products as $p)
                @php
                    $qty = (float)($p->inventory->quantity_on_hand ?? 0);
                    $outOfStock = $qty <= 0;
                    $images = $p->all_images;
                @endphp
                <div class="product-item glass-card rounded-2xl p-3.5 border border-slate-200 dark:border-slate-800 hover:border-cyan-500 flex flex-col justify-between cursor-pointer transition-all transform hover:-translate-y-1 group relative overflow-hidden select-none {{ $outOfStock ? 'opacity-60 grayscale-[40%]' : '' }}"
                    data-name="{{ strtolower($p->name) }}"
                    data-code="{{ strtolower($p->product_code) }}"
                    data-model="{{ strtolower($p->vehicle_model) }}"
                    data-brand="{{ strtolower($p->vehicle_brand) }}"
                    data-cat="{{ $p->category_id }}"
                    onclick="addToCart({{ $p->id }}, '{{ addslashes($p->name) }}', {{ $p->unit_price }}, {{ $qty }})">

                    <!-- Image with Gallery Icon and Stock Tag -->
                    <div class="relative w-full h-32 rounded-xl overflow-hidden bg-slate-100 dark:bg-dark-900 mb-2.5 border border-slate-200 dark:border-slate-800 flex items-center justify-center">
                        <img src="{{ $images[0] }}" alt="{{ $p->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

                        <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-slate-900/90 text-cyan-400 border border-cyan-500/30">
                            {{ $p->vehicle_brand }}
                        </span>

                        <!-- Multi-image click switcher trigger -->
                        @if(count($images) > 1)
                        <button type="button" onclick="event.stopPropagation(); openPosGallery({{ json_encode($images) }}, '{{ addslashes($p->name) }}')"
                            class="absolute top-2 right-2 px-2 py-0.5 rounded-md text-[10px] font-bold bg-black/70 text-white hover:bg-cyan-600 transition-colors flex items-center gap-1 shadow">
                            <i class="fas fa-images text-cyan-400"></i> {{ count($images) }}
                        </button>
                        @endif

                        <span class="absolute bottom-2 left-2 px-2 py-0.5 rounded text-[10px] font-bold {{ $qty <= $p->stock_alert_level ? ($qty <= 0 ? 'bg-rose-500 text-white' : 'bg-amber-500 text-white') : 'bg-emerald-600 text-white' }}">
                            {{ $qty <= 0 ? 'Out of Stock' : ($qty . ' ' . $p->unit_of_measure) }}
                        </span>
                    </div>

                    <!-- Details -->
                    <div>
                        <div class="text-xs font-bold text-slate-500 dark:text-slate-400 font-mono">{{ $p->product_code }}</div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white line-clamp-2 leading-snug mt-1 group-hover:text-cyan-500 transition-colors">
                            {{ $p->name }}
                        </h4>
                        @if($p->vehicle_model && $p->vehicle_model !== 'Universal')
                        <div class="text-xs text-slate-500 dark:text-slate-400 truncate mt-1">
                            <i class="fas fa-car-side text-cyan-500 mr-1"></i>{{ $p->vehicle_model }}
                        </div>
                        @endif
                    </div>

                    <!-- Price & Add Button -->
                    <div class="mt-3 pt-2.5 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <div class="text-base font-black font-display text-cyan-600 dark:text-cyan-400">
                            ₱ {{ number_format($p->unit_price, 2) }}
                        </div>
                        <span class="w-7 h-7 rounded-lg bg-cyan-500/15 text-cyan-600 dark:text-cyan-400 group-hover:bg-cyan-500 group-hover:text-white flex items-center justify-center text-xs transition-colors">
                            <i class="fas fa-plus"></i>
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN: Active Sale / Checkout Sidebar -->
    <div class="w-full lg:w-[420px] shrink-0 bg-white dark:bg-[#0c1222] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl flex flex-col overflow-hidden">
        <!-- Top Order Info & Type Toggle -->
        <div class="p-3.5 border-b border-slate-200 dark:border-slate-800 space-y-2.5 bg-slate-50/50 dark:bg-dark-850/50">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="font-bold text-sm text-slate-900 dark:text-white">Active Terminal</span>
                </div>
                <button type="button" onclick="clearCart()" class="text-xs font-semibold text-rose-500 hover:text-rose-600 flex items-center gap-1 transition-colors">
                    <i class="fas fa-trash-alt"></i> Clear Cart
                </button>
            </div>

            <!-- Order Type Selector -->
            <div class="grid grid-cols-3 gap-1 p-1 bg-slate-200/70 dark:bg-dark-900 rounded-xl font-bold text-xs">
                <button type="button" onclick="setOrderType('Walk-in')" id="typeBtn-walkin"
                    class="order-type-btn py-1.5 rounded-lg text-center bg-cyan-600 text-white shadow transition-all">
                    Walk-in
                </button>
                <button type="button" onclick="setOrderType('With Installation')" id="typeBtn-install"
                    class="order-type-btn py-1.5 rounded-lg text-center text-slate-700 dark:text-slate-300 hover:text-slate-900 transition-all">
                    + Installation
                </button>
                <button type="button" onclick="setOrderType('Pick-up / Delivery')" id="typeBtn-delivery"
                    class="order-type-btn py-1.5 rounded-lg text-center text-slate-700 dark:text-slate-300 hover:text-slate-900 transition-all">
                    Delivery
                </button>
            </div>

            <!-- Customer & Vehicle Selection -->
            <div class="space-y-1.5 text-xs">
                <select id="customerSelect" onchange="onCustomerSelect(this)"
                    class="w-full py-1.5 px-3 bg-white dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs focus:ring-1 focus:ring-cyan-500">
                    <option value="">Walk-in Customer (Unregistered)</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}" data-name="{{ $c->name }}" data-phone="{{ $c->contact_number }}" data-vehicle="{{ $c->vehicle_make_model }}" data-plate="{{ $c->plate_number }}">
                        {{ $c->name }} ({{ $c->vehicle_make_model ?: 'No vehicle' }})
                    </option>
                    @endforeach
                </select>
                <!-- Separate inputs for Name, Contact, Vehicle Model, and Plate # -->
                <div id="customerDetailsInputs" class="space-y-1.5">
                    <div class="grid grid-cols-2 gap-1.5">
                        <input type="text" id="custNameInput" placeholder="Customer Name"
                            class="py-1 px-2.5 bg-white dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-lg text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-cyan-500">
                        <input type="text" id="custPhoneInput" placeholder="Phone (e.g. 0917...)"
                            class="py-1 px-2.5 bg-white dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-lg text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-cyan-500">
                    </div>
                    <div class="grid grid-cols-2 gap-1.5">
                        <input type="text" id="custVehicleInput" placeholder="Vehicle (e.g. Fortuner)"
                            class="py-1 px-2.5 bg-white dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-lg text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-cyan-500">
                        <input type="text" id="custPlateInput" placeholder="Plate # (e.g. NBH-4821)"
                            class="py-1 px-2.5 bg-white dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-lg text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-cyan-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Cart Items List -->
        <div class="flex-1 p-3 overflow-y-auto" id="cartItemsList" data-auto-animate>
            <div id="emptyCartState" class="h-full flex flex-col items-center justify-center text-slate-400 p-8 text-center">
                <i class="fas fa-car-side text-4xl mb-3 text-slate-300 dark:text-slate-700"></i>
                <p class="font-bold text-slate-600 dark:text-slate-400 text-sm">Cart is empty</p>
                <p class="text-xs text-slate-400 mt-1">Select vehicle matting or accessories to add to cart</p>
            </div>
        </div>

        <!-- Bill Breakdown & Checkout -->
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-dark-850/70 space-y-2.5 text-xs">
            <div class="flex justify-between text-slate-600 dark:text-slate-400">
                <span>Items Subtotal:</span>
                <span class="font-semibold text-slate-900 dark:text-white" id="subtotalDisplay">₱ 0.00</span>
            </div>
            <div class="flex justify-between text-slate-600 dark:text-slate-400" id="installationFeeRow">
                <span class="flex items-center gap-1">
                    <span>Installation Fee:</span>
                    <i class="fas fa-tools text-[10px] text-cyan-500"></i>
                </span>
                <span class="font-semibold text-slate-900 dark:text-white" id="installFeeDisplay">₱ 0.00</span>
            </div>
            <div class="flex justify-between text-slate-600 dark:text-slate-400">
                <span>Discount:</span>
                <div class="flex items-center gap-1">
                    <span>- ₱</span>
                    <input type="number" id="discountInput" value="0" min="0" step="10" oninput="calculateTotals()"
                        class="w-16 py-0.5 px-1 bg-white dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded text-right text-xs font-mono">
                </div>
            </div>

            <!-- Grand Total -->
            <div class="pt-2 border-t border-slate-200 dark:border-slate-800 flex items-baseline justify-between">
                <div>
                    <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Amount Due</div>
                    <div class="text-3xl font-black font-display text-cyan-600 dark:text-cyan-400" id="grandTotalDisplay">
                        ₱ 0.00
                    </div>
                </div>
                <button type="button" onclick="openPaymentModal()" id="checkoutBtn" disabled
                    class="px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold text-sm shadow-lg shadow-cyan-500/20 disabled:opacity-40 disabled:cursor-not-allowed transition-all transform hover:-translate-y-0.5">
                    <span>Pay &amp; Checkout</span> &rarr;
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Image Switcher / Gallery for POS Products -->
<div id="posGalleryModal" class="fixed inset-0 z-50 bg-black/85 backdrop-blur-md hidden items-center justify-center p-4">
    <div class="w-full max-w-xl bg-white dark:bg-[#0c1222] border border-slate-200 dark:border-slate-700 rounded-3xl p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <h3 id="posGalleryTitle" class="text-base font-bold font-display text-slate-900 dark:text-white truncate"></h3>
            <button type="button" onclick="closePosGallery()" class="w-9 h-9 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-white text-xl flex items-center justify-center">&times;</button>
        </div>
        <div class="relative bg-slate-100 dark:bg-dark-900 rounded-2xl h-72 flex items-center justify-center p-4 overflow-hidden">
            <img id="posGalleryMainImg" src="" class="max-h-full max-w-full object-contain drop-shadow-xl">
        </div>
        <div class="flex items-center justify-center gap-2 pt-1" id="posGalleryThumbStrip"></div>
    </div>
</div>

<!-- MODAL: Payment Tender Dialog -->
<div id="paymentModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="w-full max-w-md bg-white dark:bg-[#0c1222] border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <h3 class="text-base font-bold font-display text-slate-900 dark:text-white">Payment &amp; Checkout</h3>
            <button type="button" onclick="closePaymentModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg">&times;</button>
        </div>

        <div class="p-3.5 rounded-xl bg-cyan-50 dark:bg-cyan-500/10 border border-cyan-200 dark:border-cyan-500/30 flex items-baseline justify-between">
            <span class="text-xs font-bold text-cyan-800 dark:text-cyan-300 uppercase">Total to Pay:</span>
            <span class="text-2xl font-black font-display text-cyan-600 dark:text-cyan-400" id="modalTotalDisplay">₱ 0.00</span>
        </div>

        <!-- Payment Method Selection -->
        <div class="space-y-1.5 text-xs">
            <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase">Payment Method</label>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" onclick="setPaymentMethod('Cash')" class="pay-method-btn p-2.5 rounded-xl border border-cyan-500 bg-cyan-500/10 font-bold text-cyan-600 dark:text-cyan-400 text-center">
                    <i class="fas fa-money-bill-wave mr-1"></i> Cash
                </button>
                <button type="button" onclick="setPaymentMethod('GCash / Maya')" class="pay-method-btn p-2.5 rounded-xl border border-slate-300 dark:border-slate-700 font-bold text-slate-700 dark:text-slate-300 text-center">
                    <i class="fas fa-mobile-screen mr-1 text-sky-500"></i> GCash / Maya
                </button>
                <button type="button" onclick="setPaymentMethod('Card')" class="pay-method-btn p-2.5 rounded-xl border border-slate-300 dark:border-slate-700 font-bold text-slate-700 dark:text-slate-300 text-center">
                    <i class="fas fa-credit-card mr-1 text-purple-500"></i> Card
                </button>
                <button type="button" onclick="setPaymentMethod('Bank Transfer')" class="pay-method-btn p-2.5 rounded-xl border border-slate-300 dark:border-slate-700 font-bold text-slate-700 dark:text-slate-300 text-center">
                    <i class="fas fa-building-columns mr-1 text-amber-500"></i> Bank
                </button>
            </div>
        </div>

        <!-- Reference Number (GCash / Maya / Card / Bank) -->
        <div id="paymentRefContainer" class="space-y-1 text-xs hidden">
            <label id="paymentRefLabel" class="block font-bold text-sky-600 dark:text-sky-400 uppercase flex items-center justify-between">
                <span><i class="fas fa-receipt mr-1"></i> GCash / Maya Reference No.</span>
                <span class="text-[10px] lowercase font-normal text-slate-400">(optional)</span>
            </label>
            <input type="text" id="paymentRefInput" placeholder="e.g. 1002 9384 1928 / Trx ID"
                class="w-full py-2 px-3 bg-white dark:bg-dark-900 border border-sky-300 dark:border-sky-700 rounded-xl text-slate-900 dark:text-white font-mono text-xs focus:ring-2 focus:ring-sky-500">
            <p class="text-[10px] text-slate-400">Printed on receipt for audit &amp; customer verification.</p>
        </div>

        <!-- Tendered Amount & Presets -->
        <div class="space-y-2 text-xs">
            <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase">Amount Received (₱)</label>
            <input type="number" id="tenderedInput" step="1" oninput="calculateChange()"
                class="w-full py-2.5 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono text-xl font-bold text-right focus:ring-1 focus:ring-cyan-500">

            <div class="flex items-center gap-1.5" id="tenderPresets">
                <button type="button" onclick="setTenderExact()" class="flex-1 py-1.5 bg-slate-200 dark:bg-dark-800 hover:bg-slate-300 text-slate-800 dark:text-slate-200 rounded-lg text-xs font-bold">Exact</button>
                <button type="button" onclick="addTender(500)" class="flex-1 py-1.5 bg-slate-200 dark:bg-dark-800 hover:bg-slate-300 text-slate-800 dark:text-slate-200 rounded-lg text-xs font-bold">+500</button>
                <button type="button" onclick="addTender(1000)" class="flex-1 py-1.5 bg-slate-200 dark:bg-dark-800 hover:bg-slate-300 text-slate-800 dark:text-slate-200 rounded-lg text-xs font-bold">+1,000</button>
                <button type="button" onclick="setTenderPreset(2000)" class="flex-1 py-1.5 bg-slate-200 dark:bg-dark-800 hover:bg-slate-300 text-slate-800 dark:text-slate-200 rounded-lg text-xs font-bold">₱2,000</button>
            </div>
        </div>

        <!-- Change Amount Display -->
        <div class="p-3 rounded-xl bg-slate-100 dark:bg-dark-900 border border-slate-200 dark:border-slate-800 flex items-baseline justify-between text-xs">
            <span class="font-bold text-slate-600 dark:text-slate-400 uppercase">Change Due:</span>
            <span class="text-xl font-black font-display text-emerald-600 dark:text-emerald-400" id="changeDisplay">₱ 0.00</span>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="button" onclick="submitCheckout()" id="confirmPayBtn"
                class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-white font-bold text-sm shadow-lg shadow-emerald-500/20 transition-all">
                Complete Sale &amp; Print Receipt
            </button>
        </div>
    </div>
</div>

<script>
let cart = [];
let currentOrderType = 'Walk-in';
let currentPaymentMethod = 'Cash';
let activeBrand = 'all';
let activeCategory = 'all';

// Cart Logic
function addToCart(id, name, price, stock) {
    if (stock <= 0) {
        alert('Item is currently out of stock!');
        return;
    }

    const existing = cart.find(i => i.id === id);
    if (existing) {
        if (existing.quantity + 1 > stock) {
            alert('Cannot exceed available stock on hand (' + stock + ')');
            return;
        }
        existing.quantity += 1;
    } else {
        cart.push({ id, name, price, quantity: 1, stock });
    }
    renderCart();
}

function updateQty(id, delta) {
    const item = cart.find(i => i.id === id);
    if (!item) return;

    const newQty = item.quantity + delta;
    if (newQty <= 0) {
        removeFromCart(id);
    } else {
        if (newQty > item.stock) {
            alert('Cannot exceed available stock (' + item.stock + ')');
            return;
        }
        item.quantity = newQty;
        renderCart();
    }
}

function removeFromCart(id) {
    cart = cart.filter(i => i.id !== id);
    renderCart();
}

function clearCart() {
    cart = [];
    renderCart();
}

function setOrderType(type) {
    currentOrderType = type;
    document.querySelectorAll('.order-type-btn').forEach(btn => {
        btn.className = 'order-type-btn py-1.5 rounded-lg text-center text-slate-700 dark:text-slate-300 hover:text-slate-900 transition-all';
    });

    if (type === 'Walk-in') document.getElementById('typeBtn-walkin').className = 'order-type-btn py-1.5 rounded-lg text-center bg-cyan-600 text-white shadow-sm transition-all';
    if (type === 'With Installation') document.getElementById('typeBtn-install').className = 'order-type-btn py-1.5 rounded-lg text-center bg-cyan-600 text-white shadow-sm transition-all';
    if (type === 'Pick-up / Delivery') document.getElementById('typeBtn-delivery').className = 'order-type-btn py-1.5 rounded-lg text-center bg-cyan-600 text-white shadow-sm transition-all';

    calculateTotals();
}

function renderCart() {
    const list = document.getElementById('cartItemsList');
    const badge = document.getElementById('cartCountBadge');
    badge.innerText = cart.reduce((sum, i) => sum + i.quantity, 0);

    if (cart.length === 0) {
        list.innerHTML = `
            <div id="emptyCartState" class="h-full flex flex-col items-center justify-center text-slate-400 p-8 text-center">
                <i class="fas fa-car-side text-4xl mb-3 text-slate-300 dark:text-slate-700"></i>
                <p class="font-bold text-slate-600 dark:text-slate-400 text-sm">Cart is empty</p>
                <p class="text-xs text-slate-400 mt-1">Select vehicle matting or accessories to add to cart</p>
            </div>
        `;
        document.getElementById('checkoutBtn').disabled = true;
        calculateTotals();
        return;
    }

    document.getElementById('checkoutBtn').disabled = false;
    let html = '<div class="space-y-2.5">';

    cart.forEach(item => {
        const lineTotal = item.quantity * item.price;
        html += `
            <div class="p-3 rounded-xl bg-slate-50 dark:bg-dark-900 border border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs gap-2">
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-slate-900 dark:text-white truncate">${item.name}</div>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">₱ ${item.price.toFixed(2)} each</div>
                </div>
                <div class="flex items-center gap-1.5 bg-white dark:bg-dark-800 p-1 rounded-lg border border-slate-300 dark:border-slate-700">
                    <button type="button" onclick="updateQty(${item.id}, -1)" class="w-6 h-6 flex items-center justify-center rounded text-slate-500 hover:text-slate-900 dark:hover:text-white font-bold">&minus;</button>
                    <span class="w-6 text-center font-bold text-slate-900 dark:text-white font-mono text-xs">${item.quantity}</span>
                    <button type="button" onclick="updateQty(${item.id}, 1)" class="w-6 h-6 flex items-center justify-center rounded text-slate-500 hover:text-slate-900 dark:hover:text-white font-bold">&plus;</button>
                </div>
                <div class="text-right min-w-[70px]">
                    <div class="font-black text-slate-900 dark:text-white font-display text-sm">₱ ${lineTotal.toFixed(2)}</div>
                    <button type="button" onclick="removeFromCart(${item.id})" class="text-[10px] text-rose-500 hover:underline">Remove</button>
                </div>
            </div>
        `;
    });

    html += '</div>';
    list.innerHTML = html;
    calculateTotals();
}

function calculateTotals() {
    const subtotal = cart.reduce((sum, i) => sum + (i.quantity * i.price), 0);
    const installFee = (currentOrderType === 'With Installation' && cart.length > 0) ? 300.00 : 0.00;
    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    const grandTotal = Math.max(0, subtotal + installFee - discount);

    document.getElementById('subtotalDisplay').innerText = '₱ ' + subtotal.toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('installFeeDisplay').innerText = '₱ ' + installFee.toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('grandTotalDisplay').innerText = '₱ ' + grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('modalTotalDisplay').innerText = '₱ ' + grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2});
}

function onCustomerSelect(select) {
    const opt = select.options[select.selectedIndex];
    if (select.value) {
        document.getElementById('custNameInput').value = opt.getAttribute('data-name') || '';
        document.getElementById('custPhoneInput').value = opt.getAttribute('data-phone') || '';
        document.getElementById('custVehicleInput').value = opt.getAttribute('data-vehicle') || '';
        document.getElementById('custPlateInput').value = opt.getAttribute('data-plate') || '';
    } else {
        document.getElementById('custNameInput').value = '';
        document.getElementById('custPhoneInput').value = '';
        document.getElementById('custVehicleInput').value = '';
        document.getElementById('custPlateInput').value = '';
    }
}

// Payment Dialog
function openPaymentModal() {
    if (cart.length === 0) return;
    calculateTotals();
    const modal = document.getElementById('paymentModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    // Reset reference number input
    const refInput = document.getElementById('paymentRefInput');
    if (refInput) refInput.value = '';

    const totalText = document.getElementById('grandTotalDisplay').innerText.replace(/[^\d.]/g, '');
    document.getElementById('tenderedInput').value = totalText;
    calculateChange();
}

function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function setPaymentMethod(m) {
    currentPaymentMethod = m;
    document.querySelectorAll('.pay-method-btn').forEach(btn => {
        btn.className = 'pay-method-btn p-2.5 rounded-xl border border-slate-300 dark:border-slate-700 font-bold text-slate-700 dark:text-slate-300 text-center transition-all';
    });
    event.currentTarget.className = 'pay-method-btn p-2.5 rounded-xl border border-cyan-500 bg-cyan-500/10 font-bold text-cyan-600 dark:text-cyan-400 text-center transition-all';

    const refContainer = document.getElementById('paymentRefContainer');
    const refLabel = document.getElementById('paymentRefLabel');
    const refInput = document.getElementById('paymentRefInput');

    if (m === 'Cash') {
        refContainer.classList.add('hidden');
    } else {
        refContainer.classList.remove('hidden');
        if (m === 'GCash / Maya') {
            refLabel.innerHTML = '<span><i class="fas fa-mobile-screen mr-1 text-sky-500"></i> GCash / Maya Reference No.</span><span class="text-[10px] lowercase font-normal text-slate-400">(optional)</span>';
            refInput.placeholder = 'e.g. 1002 9384 1928 / MP-9382';
        } else if (m === 'Card') {
            refLabel.innerHTML = '<span><i class="fas fa-credit-card mr-1 text-purple-500"></i> Card Approval / Slip Ref #</span><span class="text-[10px] lowercase font-normal text-slate-400">(optional)</span>';
            refInput.placeholder = 'e.g. APP-847291';
        } else {
            refLabel.innerHTML = '<span><i class="fas fa-building-columns mr-1 text-amber-500"></i> Bank Transfer Ref / Trace #</span><span class="text-[10px] lowercase font-normal text-slate-400">(optional)</span>';
            refInput.placeholder = 'e.g. BDO-TXN-882194';
        }
        // Auto-populate exact amount tendered for digital/electronic payments
        setTenderExact();
    }
}

function setTenderExact() {
    const total = parseFloat(document.getElementById('grandTotalDisplay').innerText.replace(/[^\d.]/g, '')) || 0;
    document.getElementById('tenderedInput').value = total.toFixed(2);
    calculateChange();
}

function addTender(amt) {
    const current = parseFloat(document.getElementById('tenderedInput').value) || 0;
    document.getElementById('tenderedInput').value = (current + amt).toFixed(2);
    calculateChange();
}

function setTenderPreset(preset) {
    document.getElementById('tenderedInput').value = preset.toFixed(2);
    calculateChange();
}

function calculateChange() {
    const total = parseFloat(document.getElementById('grandTotalDisplay').innerText.replace(/[^\d.]/g, '')) || 0;
    const tendered = parseFloat(document.getElementById('tenderedInput').value) || 0;
    const change = Math.max(0, tendered - total);
    document.getElementById('changeDisplay').innerText = '₱ ' + change.toLocaleString('en-US', {minimumFractionDigits: 2});
}

function submitCheckout() {
    const total = parseFloat(document.getElementById('grandTotalDisplay').innerText.replace(/[^\d.]/g, '')) || 0;
    const tendered = parseFloat(document.getElementById('tenderedInput').value) || 0;

    if (tendered < total) {
        alert('Tendered amount cannot be less than total amount due!');
        return;
    }

    const refInput = document.getElementById('paymentRefInput');
    const paymentRef = (refInput && !refInput.parentElement.classList.contains('hidden')) ? refInput.value.trim() : null;

    const custName = document.getElementById('custNameInput').value.trim();
    const custPhone = document.getElementById('custPhoneInput').value.trim();
    const vehicleModel = document.getElementById('custVehicleInput').value.trim();
    const plateNo = document.getElementById('custPlateInput').value.trim();
    const vehicleDetails = vehicleModel ? (vehicleModel + (plateNo ? ` (${plateNo})` : '')) : (plateNo || null);

    const payload = {
        cart: cart,
        order_type: currentOrderType,
        customer_id: document.getElementById('customerSelect').value || null,
        customer_name: custName || 'Walk-in Customer',
        customer_phone: custPhone || null,
        vehicle_model: vehicleModel || null,
        plate_number: plateNo || null,
        vehicle_details: vehicleDetails,
        installation_fee: (currentOrderType === 'With Installation') ? 300.00 : 0.00,
        discount_amount: parseFloat(document.getElementById('discountInput').value) || 0,
        payment_method: currentPaymentMethod,
        payment_reference: paymentRef,
        amount_tendered: tendered,
    };

    fetch("{{ route('pos.checkout') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            closePaymentModal();
            window.open(data.receipt_url, '_blank', 'width=450,height=700');
            clearCart();
            window.location.reload();
        } else {
            alert(data.message || 'Checkout failed');
        }
    })
    .catch(err => {
        alert('Server error during checkout.');
    });
}

// Filtering
function filterBrand(brand) {
    activeBrand = brand;
    document.querySelectorAll('.brand-filter-btn').forEach(btn => {
        btn.className = 'brand-filter-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-200 dark:bg-dark-800 hover:bg-slate-300 dark:hover:bg-dark-700 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 shrink-0 transition-all';
    });
    event.currentTarget.className = 'brand-filter-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-cyan-600 text-white shadow-sm shrink-0 transition-all';
    applyFilters();
}

function filterCategory(catId) {
    activeCategory = catId;
    document.querySelectorAll('.cat-filter-btn').forEach(btn => {
        btn.className = 'cat-filter-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-200 dark:bg-dark-800 hover:bg-slate-300 dark:hover:bg-dark-700 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 shrink-0 transition-all';
    });
    event.currentTarget.className = 'cat-filter-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-cyan-600 text-white shadow-sm shrink-0 transition-all';
    applyFilters();
}

document.getElementById('searchInput').addEventListener('input', applyFilters);

function applyFilters() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    let count = 0;

    document.querySelectorAll('.product-item').forEach(item => {
        const name = item.getAttribute('data-name');
        const code = item.getAttribute('data-code');
        const model = item.getAttribute('data-model');
        const brand = item.getAttribute('data-brand');
        const cat = item.getAttribute('data-cat');

        const matchSearch = !q || name.includes(q) || code.includes(q) || model.includes(q);
        const matchBrand = (activeBrand === 'all') || brand === activeBrand;
        const matchCat = (activeCategory === 'all') || cat === activeCategory;

        if (matchSearch && matchBrand && matchCat) {
            item.style.display = 'flex';
            count++;
        } else {
            item.style.display = 'none';
        }
    });

    document.getElementById('productCount').innerText = count;
}

// POS Multi-Image Gallery
function openPosGallery(images, title) {
    document.getElementById('posGalleryTitle').innerText = title;
    document.getElementById('posGalleryMainImg').src = images[0];

    const strip = document.getElementById('posGalleryThumbStrip');
    strip.innerHTML = '';

    images.forEach((url, i) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'w-12 h-12 rounded-xl border border-slate-300 dark:border-slate-700 overflow-hidden hover:border-cyan-500 transition-colors';
        btn.innerHTML = `<img src="${url}" class="w-full h-full object-cover">`;
        btn.onclick = () => {
            document.getElementById('posGalleryMainImg').src = url;
        };
        strip.appendChild(btn);
    });

    const modal = document.getElementById('posGalleryModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closePosGallery() {
    const modal = document.getElementById('posGalleryModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endsection
