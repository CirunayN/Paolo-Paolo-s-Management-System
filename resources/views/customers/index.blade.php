@extends('layouts.app')

@section('content')
<div class="space-y-6" data-auto-animate>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold font-display text-slate-900 dark:text-white flex items-center gap-3">
                <i class="fas fa-id-card text-cyan-500"></i> Customer Records
            </h2>
        </div>
        @if(auth()->user()->isAdmin())
        <button type="button" onclick="openCustomerModal()" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold text-sm shadow-lg shadow-cyan-500/20 transition-all transform hover:-translate-y-0.5">
            <i class="fas fa-plus"></i>
            <span>Add Customer Record</span>
        </button>
        @endif
    </div>

    <!-- KPI Overview Cards: Accounts & Receivables -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="glass-card rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-cyan-500/10 dark:bg-cyan-500/20 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Total Customers</p>
                <h3 class="text-xl font-bold font-display text-slate-900 dark:text-white">{{ number_format($totalCustomersCount) }}</h3>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Active Installments</p>
                <h3 class="text-xl font-bold font-display text-slate-900 dark:text-white">{{ number_format($activeInstallmentsCount) }} <span class="text-xs font-normal text-slate-400">orders</span></h3>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-4 border border-amber-200 dark:border-amber-900/50 bg-amber-500/5 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-hand-holding-dollar"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-amber-600 dark:text-amber-400 font-bold uppercase tracking-wider">Total Receivables Due</p>
                <h3 class="text-xl font-black font-display text-amber-600 dark:text-amber-400">₱ {{ number_format($totalReceivables, 2) }}</h3>
            </div>
        </div>
    </div>

    <!-- Search & Filter Form -->
    <div class="glass-card rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
        <form method="GET" action="{{ route('customers.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="hidden" name="filter" value="{{ request('filter') }}">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fas fa-search text-sm"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customer name, phone number, vehicle model, or plate #..."
                    class="w-full pl-11 pr-4 py-2.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500">
            </div>
            <button type="submit" class="px-6 py-2.5 bg-slate-900 dark:bg-dark-700 hover:bg-cyan-600 dark:hover:bg-cyan-600 text-white font-bold text-sm rounded-xl transition-colors shrink-0">
                Search
            </button>
        </form>

        <!-- Quick Filter Tabs -->
        <div class="flex items-center gap-2 pt-1 text-xs border-t border-slate-100 dark:border-slate-800/60 flex-wrap">
            <span class="text-slate-400 font-medium mr-1"><i class="fas fa-filter text-[10px]"></i> Filter:</span>
            <a href="{{ route('customers.index', ['search' => request('search')]) }}" 
               class="px-3 py-1.5 rounded-lg font-bold transition-colors {{ !request('filter') ? 'bg-cyan-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-dark-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-dark-700' }}">
                All Customers
            </a>
            <a href="{{ route('customers.index', ['filter' => 'installments', 'search' => request('search')]) }}" 
               class="px-3 py-1.5 rounded-lg font-bold transition-colors flex items-center gap-1.5 {{ request('filter') === 'installments' ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-100 dark:bg-dark-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-dark-700' }}">
                <i class="fas fa-clock text-[11px]"></i>
                <span>With Outstanding Balance</span>
                @if($activeInstallmentsCount > 0)
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ request('filter') === 'installments' ? 'bg-white text-amber-700 font-extrabold' : 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-bold' }}">
                    {{ $activeInstallmentsCount }}
                </span>
                @endif
            </a>
        </div>
    </div>

    <!-- Customer Cards Grid (Clickable to view Order History) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" data-auto-animate>
        @forelse($customers as $c)
        @php
            $custBalance = $c->orders->sum('balance_due');
        @endphp
        <div class="glass-card rounded-2xl p-5 border {{ $custBalance > 0 ? 'border-amber-300 dark:border-amber-700/60 bg-amber-500/[0.02]' : 'border-slate-200 dark:border-slate-800' }} shadow-sm hover:border-cyan-500/40 transition-all group flex flex-col justify-between cursor-pointer"
            onclick="viewCustomerHistory({{ $c->id }}, '{{ addslashes($c->name) }}', '{{ addslashes($c->vehicle_make_model ?: 'No vehicle') }}', '{{ addslashes($c->plate_number ?: '') }}')">
            <div>
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-lg group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors flex items-center gap-2">
                            <span>{{ $c->name }}</span>
                            <i class="fas fa-arrow-up-right-from-square text-xs text-cyan-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </h3>
                        <div class="font-mono text-xs font-semibold text-cyan-600 dark:text-cyan-400 mt-1">
                            <i class="fas fa-phone text-[10px] mr-1"></i>{{ $c->contact_number ?: 'No phone provided' }}
                        </div>
                    </div>
                    <div class="flex flex-col items-end gap-1 shrink-0">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-cyan-50 dark:bg-cyan-500/15 text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-500/30">
                            {{ $c->orders_count }} Orders
                        </span>
                        @if($custBalance > 0)
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900/50 flex items-center gap-1 shadow-sm">
                            <i class="fas fa-clock text-[9px]"></i> ₱{{ number_format($custBalance, 2) }} Due
                        </span>
                        @endif
                    </div>
                </div>

                <!-- Vehicle Info Box -->
                <div class="mt-4 p-3 rounded-xl bg-slate-50 dark:bg-dark-900/90 border border-slate-200 dark:border-slate-800 space-y-1.5">
                    <div class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                        <i class="fas fa-car-side text-cyan-500 text-sm"></i>
                        <span>{{ $c->vehicle_make_model ?: 'Vehicle details not specified' }}</span>
                    </div>
                    @if($c->plate_number)
                    <div class="text-xs font-mono text-slate-500 dark:text-slate-400">
                        Plate: <span class="font-bold text-slate-900 dark:text-slate-200 bg-slate-200 dark:bg-dark-800 px-2 py-0.5 rounded border border-slate-300 dark:border-slate-700">{{ $c->plate_number }}</span>
                    </div>
                    @endif
                </div>

                @if($c->address)
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-3 truncate">
                    <i class="fas fa-location-dot text-[11px] mr-1 text-slate-400"></i> {{ $c->address }}
                </div>
                @endif
            </div>

            <!-- Action footer -->
            <div class="mt-5 pt-3.5 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs gap-2">
                <div class="flex items-center gap-2">
                    <span class="text-cyan-600 dark:text-cyan-400 font-bold group-hover:underline">
                        <i class="fas fa-clock-rotate-left mr-1"></i> Orders &rarr;
                    </span>
                    @if($custBalance > 0)
                    <button type="button" onclick="event.stopPropagation(); openCollectPaymentModal({{ $c->id }}, '{{ addslashes($c->name) }}')"
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] shadow-sm shadow-emerald-600/20 transition-all cursor-pointer">
                        <i class="fas fa-hand-holding-dollar"></i>
                        <span>Collect</span>
                    </button>
                    @endif
                </div>

                @if(auth()->user()->isAdmin())
                <div class="flex items-center gap-1.5" onclick="event.stopPropagation()">
                    <button type="button" onclick="editCustomer({{ json_encode($c) }})" class="p-2 rounded-lg bg-slate-100 dark:bg-dark-800 hover:bg-slate-200 dark:hover:bg-dark-700 text-slate-600 dark:text-slate-300 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors" title="Edit Customer">
                        <i class="fas fa-pen-to-square"></i>
                    </button>
                    <button type="button" onclick="openRemoveCustomerModal({{ $c->id }}, '{{ addslashes($c->name) }}', '{{ addslashes($c->vehicle_make_model ?: 'No vehicle') }}', '{{ addslashes($c->plate_number ?: 'No plate') }}')" 
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-dark-800 hover:bg-rose-100 dark:hover:bg-rose-950/40 text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 text-xs font-bold transition-colors" title="Remove Customer Profile">
                        <i class="fas fa-trash-can text-xs"></i>
                        <span>Remove</span>
                    </button>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full p-12 glass-card rounded-2xl text-center text-slate-500">
            <i class="fas fa-user-group text-4xl mb-3 text-slate-400 dark:text-slate-600 block"></i>
            <p class="font-bold text-base text-slate-700 dark:text-slate-300">No customer profiles found</p>
            <p class="text-xs text-slate-500 mt-1">Add client records to track vehicle models and order histories.</p>
        </div>
        @endforelse
    </div>

    <div class="pt-2">
        {{ $customers->links('vendor.pagination.custom') }}
    </div>
</div>

<!-- MODAL: Customer Order History with Date Filter -->
<div id="historyModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="w-full max-w-3xl bg-white dark:bg-[#0c1222] border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-2xl space-y-5 max-h-[90vh] flex flex-col">
        <!-- History Header -->
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
            <div>
                <h3 id="historyCustName" class="text-xl font-bold font-display text-slate-900 dark:text-white">Customer History</h3>
                <p id="historyCustVehicle" class="text-xs text-cyan-600 dark:text-cyan-400 font-semibold mt-0.5"></p>
            </div>
            <button type="button" onclick="closeHistoryModal()" class="w-9 h-9 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white text-xl flex items-center justify-center">&times;</button>
        </div>

        <!-- Date Range Filter Bar -->
        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-dark-850 border border-slate-200 dark:border-slate-800 flex flex-wrap items-center gap-3 text-xs">
            <span class="font-bold text-slate-600 dark:text-slate-300"><i class="fas fa-filter text-cyan-500 mr-1"></i> Filter By Date:</span>
            <div class="flex items-center gap-1.5">
                <span class="text-slate-500">From:</span>
                <input type="date" id="historyStartDate" class="py-1.5 px-2.5 bg-white dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-lg text-xs text-slate-800 dark:text-white">
            </div>
            <div class="flex items-center gap-1.5">
                <span class="text-slate-500">To:</span>
                <input type="date" id="historyEndDate" class="py-1.5 px-2.5 bg-white dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-lg text-xs text-slate-800 dark:text-white">
            </div>
            <button type="button" onclick="loadCustomerOrders()" class="px-4 py-1.5 bg-cyan-600 hover:bg-cyan-500 text-white font-bold rounded-lg text-xs transition-colors">
                Apply Filter
            </button>
            <button type="button" onclick="resetHistoryDateFilter()" class="text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 underline">
                Clear
            </button>
            <div class="ml-auto flex items-center gap-3 font-bold text-slate-700 dark:text-slate-300">
                <span>Total Spent: <strong id="historyTotalSpent" class="text-cyan-600 dark:text-cyan-400 font-extrabold text-sm">₱ 0.00</strong></span>
                <span id="historyBalanceDueWrapper" class="hidden text-rose-600 dark:text-rose-400">| Balance Due: <strong id="historyTotalBalanceDue" class="font-black text-sm">₱ 0.00</strong></span>
            </div>
        </div>

        <!-- Orders Table / List -->
        <div class="flex-1 overflow-y-auto" id="historyOrdersContainer">
            <div class="p-12 text-center text-slate-500 text-sm">
                <i class="fas fa-spinner fa-spin text-2xl mb-2 text-cyan-500"></i>
                <p>Loading purchase history...</p>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Collect Installment Payment -->
<div id="collectPaymentModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="w-full max-w-lg bg-white dark:bg-[#0c1222] border border-slate-200 dark:border-slate-700 rounded-3xl p-6 sm:p-7 shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-200">
        <!-- Header -->
        <div class="flex items-start justify-between border-b border-slate-200 dark:border-slate-800 pb-3.5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg shrink-0">
                    <i class="fas fa-hand-holding-dollar"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold font-display text-slate-900 dark:text-white">Collect Installment Payment</h3>
                    <p id="collectCustomerName" class="text-xs text-slate-500 dark:text-slate-400 font-semibold"></p>
                </div>
            </div>
            <button type="button" onclick="closeCollectPaymentModal()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white text-xl flex items-center justify-center">&times;</button>
        </div>

        <div id="collectLoading" class="p-8 text-center text-slate-500 text-xs">
            <i class="fas fa-spinner fa-spin text-2xl mb-2 text-cyan-500"></i>
            <p>Loading active installment orders...</p>
        </div>

        <div id="collectEmpty" class="hidden p-8 text-center text-slate-500 text-xs">
            <i class="fas fa-circle-check text-3xl mb-2 text-emerald-500 block"></i>
            <p class="font-bold text-slate-700 dark:text-slate-300">All Installments Fully Paid!</p>
            <p class="text-slate-400 mt-1">This customer currently has no outstanding balance.</p>
        </div>

        <form id="collectPaymentForm" class="hidden space-y-4 text-xs" onsubmit="submitCollectPayment(event)">
            <input type="hidden" id="collectCustomerId">
            
            <!-- Order Selector (if customer has multiple active orders) -->
            <div>
                <label class="block text-slate-700 dark:text-slate-300 font-bold mb-1">Select Order / Invoice</label>
                <select id="collectOrderSelect" onchange="handleCollectOrderChange()" class="w-full py-2.5 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-cyan-500">
                </select>
            </div>

            <!-- Selected Order Summary Box -->
            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-dark-900 border border-slate-200 dark:border-slate-800 space-y-2">
                <div class="flex justify-between items-center text-slate-500 dark:text-slate-400">
                    <span>Order Total:</span>
                    <strong id="collectOrderTotal" class="text-slate-800 dark:text-slate-200 font-mono">₱ 0.00</strong>
                </div>
                <div class="flex justify-between items-center text-slate-500 dark:text-slate-400">
                    <span>Already Paid:</span>
                    <span id="collectOrderPaid" class="text-emerald-600 dark:text-emerald-400 font-mono font-bold">₱ 0.00</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-slate-200 dark:border-slate-800">
                    <span class="font-bold text-rose-600 dark:text-rose-400">Remaining Balance Due:</span>
                    <strong id="collectOrderBalance" class="text-rose-600 dark:text-rose-400 font-mono text-base font-black">₱ 0.00</strong>
                </div>
            </div>

            <!-- Amount to Collect -->
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label class="text-slate-700 dark:text-slate-300 font-bold">Amount to Collect Today <span class="text-rose-500">*</span></label>
                    <button type="button" onclick="setCollectFullBalance()" class="text-[11px] text-cyan-600 dark:text-cyan-400 hover:underline font-bold">Pay Full Balance</button>
                </div>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-bold">₱</span>
                    <input type="number" step="0.01" min="0.01" id="collectAmountInput" required
                        class="w-full pl-8 pr-4 py-2.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-bold text-sm focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <!-- Payment Method Radios -->
            <div>
                <label class="block text-slate-700 dark:text-slate-300 font-bold mb-1.5">Payment Method <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <label class="cursor-pointer border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-center transition-all has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-500/10">
                        <input type="radio" name="collect_method" value="Cash" checked class="hidden" onchange="handleCollectMethodChange('Cash')">
                        <i class="fas fa-money-bill-wave text-base mb-1 block text-emerald-500"></i>
                        <span class="font-bold text-slate-800 dark:text-slate-200 text-[11px]">Cash</span>
                    </label>
                    <label class="cursor-pointer border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-center transition-all has-[:checked]:border-blue-500 has-[:checked]:bg-blue-500/10">
                        <input type="radio" name="collect_method" value="GCash / Maya" class="hidden" onchange="handleCollectMethodChange('GCash / Maya')">
                        <i class="fas fa-mobile-screen text-base mb-1 block text-blue-500"></i>
                        <span class="font-bold text-slate-800 dark:text-slate-200 text-[11px]">GCash</span>
                    </label>
                    <label class="cursor-pointer border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-center transition-all has-[:checked]:border-purple-500 has-[:checked]:bg-purple-500/10">
                        <input type="radio" name="collect_method" value="Card" class="hidden" onchange="handleCollectMethodChange('Card')">
                        <i class="fas fa-credit-card text-base mb-1 block text-purple-500"></i>
                        <span class="font-bold text-slate-800 dark:text-slate-200 text-[11px]">Card</span>
                    </label>
                    <label class="cursor-pointer border border-slate-300 dark:border-slate-700 rounded-xl p-2.5 text-center transition-all has-[:checked]:border-amber-500 has-[:checked]:bg-amber-500/10">
                        <input type="radio" name="collect_method" value="Bank Transfer" class="hidden" onchange="handleCollectMethodChange('Bank Transfer')">
                        <i class="fas fa-building-columns text-base mb-1 block text-amber-500"></i>
                        <span class="font-bold text-slate-800 dark:text-slate-200 text-[11px]">Bank</span>
                    </label>
                </div>
            </div>

            <!-- Payment Reference -->
            <div id="collectRefWrapper" class="hidden">
                <label class="block text-slate-700 dark:text-slate-300 font-bold mb-1">Reference No / Trx ID <span class="text-rose-500">*</span></label>
                <input type="text" id="collectRefInput" placeholder="e.g. 10029384728"
                    class="w-full py-2 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs font-mono focus:ring-1 focus:ring-cyan-500">
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-slate-700 dark:text-slate-300 font-semibold mb-1">Payment Note (Optional)</label>
                <input type="text" id="collectNotesInput" placeholder="e.g. Installment collection received at shop"
                    class="w-full py-2 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs focus:ring-1 focus:ring-cyan-500">
            </div>

            <div id="collectErrorAlert" class="hidden p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 text-rose-600 dark:text-rose-400 text-xs font-semibold">
            </div>

            <!-- Submit buttons -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeCollectPaymentModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-dark-800 text-slate-700 dark:text-slate-300 font-semibold text-xs hover:bg-slate-200 dark:hover:bg-dark-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" id="collectSubmitBtn" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center gap-1.5">
                    <i class="fas fa-check"></i>
                    <span>Confirm Payment</span>
                </button>
            </div>
        </form>

        <!-- Success view after collection -->
        <div id="collectSuccessBox" class="hidden p-6 text-center space-y-4">
            <div class="w-14 h-14 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-500 mx-auto flex items-center justify-center text-2xl">
                <i class="fas fa-check-double"></i>
            </div>
            <div>
                <h4 class="text-base font-bold text-slate-900 dark:text-white">Payment Recorded Successfully!</h4>
                <p id="collectSuccessMsg" class="text-xs text-slate-500 dark:text-slate-400 mt-1"></p>
            </div>
            <div class="flex items-center justify-center gap-3 pt-2">
                <a id="collectReceiptLink" href="#" target="_blank" class="px-4 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs inline-flex items-center gap-1.5 shadow-md">
                    <i class="fas fa-print"></i>
                    <span>Print Receipt</span>
                </a>
                <button type="button" onclick="closeAndReloadAfterCollect()" class="px-4 py-2.5 rounded-xl bg-slate-200 dark:bg-dark-800 text-slate-700 dark:text-slate-300 font-semibold text-xs">
                    Done
                </button>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->isAdmin())
<!-- MODAL: Add / Edit Customer Record (Admin Only) -->
<div id="customerModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="w-full max-w-md bg-white dark:bg-[#0c1222] border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <h3 id="custModalTitle" class="text-base font-bold font-display text-slate-900 dark:text-white">Add Customer Record</h3>
            <button type="button" onclick="closeCustomerModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">&times;</button>
        </div>

        <form id="customerForm" method="POST" action="{{ route('customers.store') }}">
            @csrf
            <input type="hidden" name="_method" id="custFormMethod" value="POST">

            <div class="space-y-3 text-xs">
                <div>
                    <label class="block text-slate-700 dark:text-slate-300 font-semibold mb-1">Customer Full Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="custName" required placeholder="e.g. Juan Dela Cruz"
                        class="w-full py-2 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs focus:ring-1 focus:ring-cyan-500">
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 font-semibold mb-1">Contact Phone</label>
                        <input type="text" name="contact_number" id="custPhone" placeholder="0917-000-0000"
                            class="w-full py-2 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs focus:ring-1 focus:ring-cyan-500">
                    </div>
                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 font-semibold mb-1">Email</label>
                        <input type="email" name="email" id="custEmail" placeholder="client@email.com"
                            class="w-full py-2 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs focus:ring-1 focus:ring-cyan-500">
                    </div>
                </div>

                <div>
                    <label class="block text-slate-700 dark:text-slate-300 font-semibold mb-1">Vehicle Make &amp; Model</label>
                    <input type="text" name="vehicle_make_model" id="custVehicle" placeholder="e.g. Toyota Fortuner 2023 / Honda Civic"
                        class="w-full py-2 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs focus:ring-1 focus:ring-cyan-500">
                </div>

                <div>
                    <label class="block text-slate-700 dark:text-slate-300 font-semibold mb-1">Vehicle Plate Number</label>
                    <input type="text" name="plate_number" id="custPlate" placeholder="e.g. NBH-4821"
                        class="w-full py-2 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs uppercase font-mono focus:ring-1 focus:ring-cyan-500">
                </div>

                <div>
                    <label class="block text-slate-700 dark:text-slate-300 font-semibold mb-1">Davao City Location / Barangay</label>
                    <input type="text" name="address" id="custAddress" list="davaoLocationsList" placeholder="e.g. Matina, Davao City"
                        class="w-full py-2 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs focus:ring-1 focus:ring-cyan-500">
                    <datalist id="davaoLocationsList">
                        <option value="Poblacion, Davao City">
                        <option value="Matina, Davao City">
                        <option value="Buhangin, Davao City">
                        <option value="Bajada (J.P. Laurel), Davao City">
                        <option value="Lanang, Davao City">
                        <option value="Toril, Davao City">
                        <option value="Ecoland, Davao City">
                        <option value="Bangkal, Davao City">
                        <option value="Agdao, Davao City">
                        <option value="Panacan, Davao City">
                        <option value="Calinan, Davao City">
                        <option value="Sasa, Davao City">
                        <option value="Cabantian, Davao City">
                        <option value="Maa, Davao City">
                        <option value="Tibungco, Davao City">
                        <option value="Bago Aplaya, Davao City">
                        <option value="Mintal, Davao City">
                    </datalist>
                    <p class="text-[10px] text-slate-400 mt-1">Paolo Paolo serves Davao City and surrounding districts.</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 mt-4 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeCustomerModal()" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-dark-800 text-slate-700 dark:text-slate-300 font-semibold text-xs">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs">
                    Save Record
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
let currentActiveCustId = null;

function viewCustomerHistory(id, name, vehicle, plate) {
    currentActiveCustId = id;
    document.getElementById('historyCustName').innerText = name;
    document.getElementById('historyCustVehicle').innerText = vehicle + (plate ? ` (${plate})` : '');
    document.getElementById('historyStartDate').value = '';
    document.getElementById('historyEndDate').value = '';

    const modal = document.getElementById('historyModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    loadCustomerOrders();
}

function closeHistoryModal() {
    const modal = document.getElementById('historyModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function resetHistoryDateFilter() {
    document.getElementById('historyStartDate').value = '';
    document.getElementById('historyEndDate').value = '';
    loadCustomerOrders();
}

function escapeQuote(str) {
    if (!str) return '';
    return String(str).replace(/'/g, "\\'").replace(/"/g, '&quot;');
}

function loadCustomerOrders() {
    if (!currentActiveCustId) return;

    const container = document.getElementById('historyOrdersContainer');
    container.innerHTML = `
        <div class="p-12 text-center text-slate-500 text-sm">
            <i class="fas fa-spinner fa-spin text-2xl mb-2 text-cyan-500"></i>
            <p>Loading purchase history...</p>
        </div>
    `;

    const start = document.getElementById('historyStartDate').value;
    const end = document.getElementById('historyEndDate').value;
    let url = `/customers/${currentActiveCustId}/orders`;
    const params = new URLSearchParams();
    if (start) params.append('start_date', start);
    if (end) params.append('end_date', end);
    if (params.toString()) url += `?${params.toString()}`;

    fetch(url)
        .then(res => res.json())
        .then(data => {
            document.getElementById('historyTotalSpent').innerText = '₱ ' + Number(data.total_spent).toLocaleString('en-US', {minimumFractionDigits: 2});
            
            const balWrapper = document.getElementById('historyBalanceDueWrapper');
            const balDueEl = document.getElementById('historyTotalBalanceDue');
            if (data.total_balance_due > 0) {
                balWrapper.classList.remove('hidden');
                balDueEl.innerText = '₱ ' + Number(data.total_balance_due).toLocaleString('en-US', {minimumFractionDigits: 2});
            } else {
                balWrapper.classList.add('hidden');
            }

            if (!data.orders || data.orders.length === 0) {
                container.innerHTML = `
                    <div class="p-12 text-center text-slate-500 text-sm">
                        <i class="fas fa-receipt text-3xl mb-2 text-slate-400 block"></i>
                        <p class="font-bold text-slate-700 dark:text-slate-300">No past orders found</p>
                        <p class="text-xs text-slate-400 mt-1">Try adjusting your date filters.</p>
                    </div>
                `;
                return;
            }

            let html = `
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-100 dark:bg-dark-850 text-slate-500 dark:text-slate-400 uppercase tracking-wider text-[11px] font-bold border-b border-slate-200 dark:border-slate-800">
                            <th class="py-2.5 px-3">Invoice #</th>
                            <th class="py-2.5 px-3">Date &amp; Time</th>
                            <th class="py-2.5 px-3">Items &amp; Payment Ledger</th>
                            <th class="py-2.5 px-3">Payment Plan</th>
                            <th class="py-2.5 px-3 text-right">Total Amount</th>
                            <th class="py-2.5 px-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
            `;

            data.orders.forEach(order => {
                const date = new Date(order.created_at).toLocaleDateString('en-US', {
                    year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
                });

                let itemsList = '';
                if (order.items && order.items.length > 0) {
                    itemsList = order.items.map(i => `<div class="truncate max-w-xs font-medium text-slate-700 dark:text-slate-300">${Number(i.quantity)}x ${i.product_name}</div>`).join('');
                } else {
                    itemsList = '<span class="text-slate-400">Order items</span>';
                }

                const isInstallment = order.payment_type === 'Installment';
                const balanceDue = Number(order.balance_due || 0);
                const amountPaid = Number(order.amount_paid || order.total_amount);

                let paymentPlanBadge = '';
                if (isInstallment) {
                    if (balanceDue > 0) {
                        paymentPlanBadge = `
                            <div>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-900 flex items-center gap-1 w-max">
                                    <i class="fas fa-clock text-[9px]"></i> ₱${balanceDue.toLocaleString('en-US', {minimumFractionDigits: 2})} Due
                                </span>
                                <div class="text-[10px] text-slate-400 mt-1">Paid: ₱${amountPaid.toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
                            </div>
                        `;
                    } else {
                        paymentPlanBadge = `
                            <div>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900 flex items-center gap-1 w-max">
                                    <i class="fas fa-check-circle text-[9px]"></i> Fully Paid
                                </span>
                                <div class="text-[10px] text-slate-400 mt-1">Total: ₱${amountPaid.toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
                            </div>
                        `;
                    }
                } else {
                    paymentPlanBadge = `
                        <div>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-dark-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                Full Payment
                            </span>
                            <div class="text-[10px] text-slate-400 mt-1">${order.payment_method}</div>
                        </div>
                    `;
                }

                // Payment history sub-ledger if payments exists
                let paymentLedgerHtml = '';
                if (order.payments && order.payments.length > 0) {
                    const pList = order.payments.map(p => {
                        const pDate = new Date(p.payment_date || p.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                        return `<span class="bg-slate-100 dark:bg-dark-900 border border-slate-200 dark:border-slate-800 px-2 py-0.5 rounded text-[10px] text-slate-600 dark:text-slate-300">
                            #${p.payment_number}: <strong>₱${Number(p.amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</strong> (${p.payment_method} • ${pDate})
                        </span>`;
                    }).join(' ');
                    paymentLedgerHtml = `<div class="mt-2 pt-1 border-t border-slate-100 dark:border-slate-800/60 flex flex-wrap gap-1 items-center"><span class="text-[10px] text-slate-400 font-semibold">Ledger:</span> ${pList}</div>`;
                }

                let collectBtn = '';
                if (balanceDue > 0) {
                    collectBtn = `
                        <button type="button" onclick="closeHistoryModal(); openCollectPaymentModal(${order.customer_id}, '${escapeQuote(data.customer.name)}', ${order.id})" 
                            class="px-2 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px] shadow-sm flex items-center gap-1 shrink-0 cursor-pointer" title="Collect Payment on this Order">
                            <i class="fas fa-hand-holding-dollar"></i> Collect
                        </button>
                    `;
                }

                html += `
                    <tr class="hover:bg-slate-50 dark:hover:bg-dark-800/40 transition-colors">
                        <td class="py-3 px-3">
                            <div class="font-mono font-bold text-cyan-600 dark:text-cyan-400">${order.invoice_no}</div>
                            <div class="text-[10px] text-slate-400 uppercase font-semibold">${order.order_type}</div>
                        </td>
                        <td class="py-3 px-3 text-slate-500 dark:text-slate-400 font-mono text-[11px] whitespace-nowrap">${date}</td>
                        <td class="py-3 px-3">
                            ${itemsList}
                            ${paymentLedgerHtml}
                        </td>
                        <td class="py-3 px-3">
                            ${paymentPlanBadge}
                        </td>
                        <td class="py-3 px-3 text-right font-black font-display text-slate-900 dark:text-white text-sm whitespace-nowrap">₱ ${Number(order.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                        <td class="py-3 px-3 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="/pos/receipt/${order.id}" target="_blank" class="p-1.5 rounded-lg bg-slate-100 dark:bg-dark-800 hover:bg-slate-200 dark:hover:bg-dark-700 text-slate-600 dark:text-slate-300 hover:text-cyan-500 transition-colors" title="Print Receipt">
                                    <i class="fas fa-print"></i>
                                </a>
                                ${collectBtn}
                            </div>
                        </td>
                    </tr>
                `;
            });

            html += `</tbody></table>`;
            container.innerHTML = html;
        })
        .catch(err => {
            container.innerHTML = `<div class="p-8 text-center text-rose-500 text-xs">Error loading customer order history.</div>`;
        });
}

// ----------------------------------------
// Collect Installment Payment Logic
// ----------------------------------------
let currentCustomerInstallmentOrders = [];

function openCollectPaymentModal(customerId, customerName, preselectOrderId = null) {
    document.getElementById('collectCustomerId').value = customerId;
    document.getElementById('collectCustomerName').innerText = customerName;
    document.getElementById('collectLoading').classList.remove('hidden');
    document.getElementById('collectEmpty').classList.add('hidden');
    document.getElementById('collectPaymentForm').classList.add('hidden');
    document.getElementById('collectSuccessBox').classList.add('hidden');
    document.getElementById('collectNotesInput').value = '';
    document.getElementById('collectRefInput').value = '';
    document.getElementById('collectErrorAlert').classList.add('hidden');

    const modal = document.getElementById('collectPaymentModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    fetch(`/customers/${customerId}/installments`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('collectLoading').classList.add('hidden');
            if (!data.orders || data.orders.length === 0) {
                document.getElementById('collectEmpty').classList.remove('hidden');
                return;
            }

            currentCustomerInstallmentOrders = data.orders;
            const select = document.getElementById('collectOrderSelect');
            select.innerHTML = '';

            data.orders.forEach(ord => {
                const opt = document.createElement('option');
                opt.value = ord.id;
                opt.textContent = `${ord.invoice_no} — Balance: ₱${Number(ord.balance_due).toLocaleString('en-US', {minimumFractionDigits: 2})} (Total: ₱${Number(ord.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2})})`;
                if (preselectOrderId && ord.id == preselectOrderId) {
                    opt.selected = true;
                }
                select.appendChild(opt);
            });

            document.getElementById('collectPaymentForm').classList.remove('hidden');
            handleCollectOrderChange();
        })
        .catch(err => {
            document.getElementById('collectLoading').innerHTML = '<div class="text-rose-500 text-xs p-4">Error loading active customer installments.</div>';
        });
}

function closeCollectPaymentModal() {
    const modal = document.getElementById('collectPaymentModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function handleCollectOrderChange() {
    const orderId = document.getElementById('collectOrderSelect').value;
    const order = currentCustomerInstallmentOrders.find(o => o.id == orderId);
    if (!order) return;

    const total = Number(order.total_amount || 0);
    const paid = Number(order.amount_paid || 0);
    const balance = Number(order.balance_due || 0);

    document.getElementById('collectOrderTotal').innerText = '₱ ' + total.toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('collectOrderPaid').innerText = '₱ ' + paid.toLocaleString('en-US', {minimumFractionDigits: 2});
    document.getElementById('collectOrderBalance').innerText = '₱ ' + balance.toLocaleString('en-US', {minimumFractionDigits: 2});

    const amountInput = document.getElementById('collectAmountInput');
    amountInput.value = balance.toFixed(2);
    amountInput.max = balance;
}

function setCollectFullBalance() {
    const orderId = document.getElementById('collectOrderSelect').value;
    const order = currentCustomerInstallmentOrders.find(o => o.id == orderId);
    if (!order) return;
    document.getElementById('collectAmountInput').value = Number(order.balance_due).toFixed(2);
}

function handleCollectMethodChange(method) {
    const refWrapper = document.getElementById('collectRefWrapper');
    const refInput = document.getElementById('collectRefInput');
    if (method === 'Cash') {
        refWrapper.classList.add('hidden');
        refInput.removeAttribute('required');
    } else {
        refWrapper.classList.remove('hidden');
        refInput.setAttribute('required', 'required');
    }
}

function submitCollectPayment(e) {
    e.preventDefault();
    const customerId = document.getElementById('collectCustomerId').value;
    const orderId = document.getElementById('collectOrderSelect').value;
    const amount = parseFloat(document.getElementById('collectAmountInput').value);
    const methodEl = document.querySelector('input[name="collect_method"]:checked');
    const paymentMethod = methodEl ? methodEl.value : 'Cash';
    const reference = document.getElementById('collectRefInput').value;
    const notes = document.getElementById('collectNotesInput').value;
    const errorAlert = document.getElementById('collectErrorAlert');

    errorAlert.classList.add('hidden');

    if (!amount || amount <= 0) {
        errorAlert.innerText = 'Please enter a valid payment amount.';
        errorAlert.classList.remove('hidden');
        return;
    }

    const order = currentCustomerInstallmentOrders.find(o => o.id == orderId);
    if (order && amount > Number(order.balance_due)) {
        errorAlert.innerText = `Payment amount cannot exceed remaining balance due of ₱${Number(order.balance_due).toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        errorAlert.classList.remove('hidden');
        return;
    }

    const submitBtn = document.getElementById('collectSubmitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin mr-1"></i> Processing...`;

    fetch(`/customers/${customerId}/collect-payment`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            order_id: orderId,
            amount: amount,
            payment_method: paymentMethod,
            payment_reference: reference,
            notes: notes
        })
    })
    .then(res => res.json())
    .then(data => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = `<i class="fas fa-check"></i> <span>Confirm Payment</span>`;

        if (data.success) {
            document.getElementById('collectPaymentForm').classList.add('hidden');
            document.getElementById('collectSuccessBox').classList.remove('hidden');
            document.getElementById('collectSuccessMsg').innerText = data.message;
            document.getElementById('collectReceiptLink').href = data.receipt_url;
        } else {
            errorAlert.innerText = data.message || 'An error occurred while saving payment.';
            errorAlert.classList.remove('hidden');
        }
    })
    .catch(err => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = `<i class="fas fa-check"></i> <span>Confirm Payment</span>`;
        errorAlert.innerText = 'Network error. Please try again.';
        errorAlert.classList.remove('hidden');
    });
}

function closeAndReloadAfterCollect() {
    window.location.reload();
}

function openCustomerModal() {
    document.getElementById('custModalTitle').innerText = 'Add Customer Record';
    document.getElementById('customerForm').action = "{{ route('customers.store') }}";
    document.getElementById('custFormMethod').value = 'POST';
    document.getElementById('customerForm').reset();

    const modal = document.getElementById('customerModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function editCustomer(c) {
    document.getElementById('custModalTitle').innerText = 'Edit Customer: ' + c.name;
    document.getElementById('customerForm').action = '/customers/' + c.id;
    document.getElementById('custFormMethod').value = 'PUT';

    document.getElementById('custName').value = c.name || '';
    document.getElementById('custPhone').value = c.contact_number || '';
    document.getElementById('custEmail').value = c.email || '';
    document.getElementById('custVehicle').value = c.vehicle_make_model || '';
    document.getElementById('custPlate').value = c.plate_number || '';
    document.getElementById('custAddress').value = c.address || '';

    const modal = document.getElementById('customerModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeCustomerModal() {
    const modal = document.getElementById('customerModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function openRemoveCustomerModal(id, name, vehicle, plate) {
    document.getElementById('removeCustName').innerText = name;
    document.getElementById('removeCustDetails').innerText = `${vehicle} • Plate: ${plate}`;
    document.getElementById('removeCustForm').action = '/customers/' + id;
    const modal = document.getElementById('removeCustomerModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeRemoveCustomerModal() {
    const modal = document.getElementById('removeCustomerModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>

<!-- CONFIRMATION MODAL: Remove Customer Profile -->
<div id="removeCustomerModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="w-full max-w-md bg-white dark:bg-[#0c1222] border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-7 shadow-2xl space-y-5 animate-in fade-in zoom-in-95 duration-200">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xl shrink-0 border border-rose-500/20">
                <i class="fas fa-box-archive"></i>
            </div>
            <div class="space-y-1">
                <h3 class="text-lg font-bold font-display text-slate-900 dark:text-white">Remove Customer Profile</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Are you sure you want to move this client to Archive / Trash?
                </p>
            </div>
        </div>

        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-dark-900 border border-slate-200 dark:border-slate-800 space-y-1">
            <div class="text-xs font-bold text-slate-900 dark:text-white" id="removeCustName">Customer Name</div>
            <div class="text-[11px] font-mono text-cyan-600 dark:text-cyan-400" id="removeCustDetails">Vehicle details</div>
        </div>

        <div class="p-3 rounded-xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900/50 text-[11px] text-blue-700 dark:text-blue-300 flex items-start gap-2">
            <i class="fas fa-shield-check text-blue-500 mt-0.5"></i>
            <span><strong>Safe Archiving:</strong> Historical orders, receipts, and vehicle service logs will remain completely intact. You can restore this customer at any time from the Trash tab.</span>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="button" onclick="closeRemoveCustomerModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-dark-800 text-slate-700 dark:text-slate-300 font-semibold text-xs hover:bg-slate-200 dark:hover:bg-dark-700 transition-colors cursor-pointer">
                Cancel
            </button>
            <form id="removeCustForm" method="POST" action="" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-500 to-red-600 hover:from-rose-600 hover:to-red-700 text-white font-bold text-xs shadow-lg shadow-rose-500/20 transition-all cursor-pointer flex items-center gap-1.5">
                    <i class="fas fa-trash-can"></i>
                    <span>Remove</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
