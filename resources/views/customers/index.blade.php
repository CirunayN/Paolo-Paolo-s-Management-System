@extends('layouts.app')

@section('content')
<div class="space-y-6" data-auto-animate>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold font-display text-slate-900 dark:text-white flex items-center gap-3">
                <i class="fas fa-id-card text-cyan-500"></i> Customer Records
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Client directory with vehicle models, plate numbers, and purchase order histories
            </p>
        </div>
        @if(auth()->user()->isAdmin())
        <button type="button" onclick="openCustomerModal()" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold text-sm shadow-lg shadow-cyan-500/20 transition-all transform hover:-translate-y-0.5">
            <i class="fas fa-plus"></i>
            <span>Add Customer Record</span>
        </button>
        @endif
    </div>

    <!-- Search Form -->
    <div class="glass-card rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('customers.index') }}" class="flex gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fas fa-search text-sm"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customer name, phone number, vehicle model, or plate #..."
                    class="w-full pl-11 pr-4 py-2.5 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500">
            </div>
            <button type="submit" class="px-6 py-2.5 bg-slate-900 dark:bg-dark-700 hover:bg-cyan-600 dark:hover:bg-cyan-600 text-white font-bold text-sm rounded-xl transition-colors">
                Search
            </button>
        </form>
    </div>

    <!-- Customer Cards Grid (Clickable to view Order History) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" data-auto-animate>
        @forelse($customers as $c)
        <div class="glass-card rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm hover:border-cyan-500/40 transition-all group flex flex-col justify-between cursor-pointer"
            onclick="viewCustomerHistory({{ $c->id }}, '{{ addslashes($c->name) }}', '{{ addslashes($c->vehicle_make_model ?: 'No vehicle') }}', '{{ addslashes($c->plate_number ?: '') }}')">
            <div>
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-lg group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition-colors flex items-center gap-2">
                            <span>{{ $c->name }}</span>
                            <i class="fas fa-arrow-up-right-from-square text-xs text-cyan-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </h3>
                        <div class="font-mono text-xs font-semibold text-cyan-600 dark:text-cyan-400 mt-1">
                            <i class="fas fa-phone text-[10px] mr-1"></i>{{ $c->contact_number ?: 'No phone provided' }}
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-cyan-50 dark:bg-cyan-500/15 text-cyan-700 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-500/30">
                        {{ $c->orders_count }} Orders
                    </span>
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
            <div class="mt-5 pt-3.5 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs">
                <span class="text-cyan-600 dark:text-cyan-400 font-bold group-hover:underline">
                    <i class="fas fa-clock-rotate-left mr-1"></i> View Order History &rarr;
                </span>

                @if(auth()->user()->isAdmin())
                <div class="flex items-center gap-2" onclick="event.stopPropagation()">
                    <button type="button" onclick="editCustomer({{ json_encode($c) }})" class="p-2 rounded-lg bg-slate-100 dark:bg-dark-800 hover:bg-slate-200 dark:hover:bg-dark-700 text-slate-600 dark:text-slate-300 hover:text-cyan-600 dark:hover:text-cyan-400 transition-colors" title="Edit Customer">
                        <i class="fas fa-pen-to-square"></i>
                    </button>
                    <form method="POST" action="{{ route('customers.destroy', $c->id) }}" class="inline" onsubmit="return confirm('Delete customer record for {{ addslashes($c->name) }}?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 rounded-lg bg-slate-100 dark:bg-dark-800 hover:bg-rose-100 dark:hover:bg-rose-950/40 text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 transition-colors" title="Delete Record">
                            <i class="fas fa-trash-can"></i>
                        </button>
                    </form>
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
        {{ $customers->links() }}
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
            <div class="ml-auto font-bold text-slate-700 dark:text-slate-300">
                Total Spent: <span id="historyTotalSpent" class="text-cyan-600 dark:text-cyan-400 font-extrabold text-sm">₱ 0.00</span>
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
                    <label class="block text-slate-700 dark:text-slate-300 font-semibold mb-1">Address</label>
                    <input type="text" name="address" id="custAddress" placeholder="City or Barangay"
                        class="w-full py-2 px-3 bg-slate-50 dark:bg-dark-900 border border-slate-300 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-xs focus:ring-1 focus:ring-cyan-500">
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
                            <th class="py-2.5 px-3">Items Purchased</th>
                            <th class="py-2.5 px-3">Payment</th>
                            <th class="py-2.5 px-3 text-right">Total Amount</th>
                            <th class="py-2.5 px-3 text-center">Receipt</th>
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

                html += `
                    <tr class="hover:bg-slate-50 dark:hover:bg-dark-800/40 transition-colors">
                        <td class="py-3 px-3 font-mono font-bold text-cyan-600 dark:text-cyan-400">${order.invoice_no}</td>
                        <td class="py-3 px-3 text-slate-500 dark:text-slate-400 font-mono text-[11px]">${date}</td>
                        <td class="py-3 px-3">${itemsList}</td>
                        <td class="py-3 px-3 font-semibold text-slate-700 dark:text-slate-300">${order.payment_method}</td>
                        <td class="py-3 px-3 text-right font-black font-display text-slate-900 dark:text-white text-sm">₱ ${Number(order.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                        <td class="py-3 px-3 text-center">
                            <a href="/pos/receipt/${order.id}" target="_blank" class="p-1.5 rounded-lg bg-slate-100 dark:bg-dark-800 hover:bg-slate-200 dark:hover:bg-dark-700 text-slate-600 dark:text-slate-300 hover:text-cyan-500 transition-colors">
                                <i class="fas fa-print"></i>
                            </a>
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
</script>
@endsection
