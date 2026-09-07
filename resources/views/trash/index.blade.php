@extends('layouts.app')

@section('title', 'Trash / Archive Manager')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center text-lg">
                    <i class="fas fa-box-archive"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold font-display text-slate-900 dark:text-white">Trash / Archive Manager</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Safely review and restore removed products and customer records</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('products.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-dark-800 hover:bg-slate-200 dark:hover:bg-dark-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition-colors">
                <i class="fas fa-boxes-stacked mr-1.5"></i> Back to Inventory
            </a>
            <a href="{{ route('customers.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-dark-800 hover:bg-slate-200 dark:hover:bg-dark-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition-colors">
                <i class="fas fa-id-card mr-1.5"></i> Back to Customers
            </a>
        </div>
    </div>

    <!-- Safety Banner -->
    <div class="p-4 rounded-2xl bg-cyan-500/10 border border-cyan-500/25 flex items-start gap-3.5">
        <div class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-sm shrink-0 mt-0.5">
            <i class="fas fa-shield-halved"></i>
        </div>
        <div class="text-xs">
            <div class="font-bold text-slate-900 dark:text-white text-sm">Safe Data Retention (Soft Deletes Active)</div>
            <p class="text-slate-600 dark:text-slate-300 mt-0.5">
                When products or customer records are removed, they are safely preserved in this archive. 
                <strong>Historical orders, customer sales receipts, financial summaries, and inventory logs remain 100% intact and linked</strong>. 
                Clicking <span class="font-bold text-emerald-600 dark:text-emerald-400">"Restore"</span> immediately reactivates the record.
            </p>
        </div>
    </div>

    <!-- Main Card with Tabs -->
    <div class="glass-card rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xl">
        <!-- Tabs Header -->
        <div class="flex border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-dark-850 px-4 pt-3 gap-2">
            <a href="{{ route('trash.index', ['tab' => 'products']) }}" 
               class="px-5 py-2.5 rounded-t-xl text-xs font-bold transition-all border-b-2 flex items-center gap-2 {{ $activeTab === 'products' ? 'border-cyan-500 text-cyan-600 dark:text-cyan-400 bg-white dark:bg-dark-900' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}">
                <i class="fas fa-boxes-stacked"></i>
                <span>Archived Products</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $activeTab === 'products' ? 'bg-cyan-500 text-white' : 'bg-slate-200 dark:bg-dark-700 text-slate-600 dark:text-slate-400' }}">
                    {{ $archivedProductsCount }}
                </span>
            </a>

            <a href="{{ route('trash.index', ['tab' => 'customers']) }}" 
               class="px-5 py-2.5 rounded-t-xl text-xs font-bold transition-all border-b-2 flex items-center gap-2 {{ $activeTab === 'customers' ? 'border-cyan-500 text-cyan-600 dark:text-cyan-400 bg-white dark:bg-dark-900' : 'border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-200' }}">
                <i class="fas fa-users"></i>
                <span>Archived Customer Profiles</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $activeTab === 'customers' ? 'bg-cyan-500 text-white' : 'bg-slate-200 dark:bg-dark-700 text-slate-600 dark:text-slate-400' }}">
                    {{ $archivedCustomersCount }}
                </span>
            </a>
        </div>

        <div class="p-5">
            @if($activeTab === 'products')
            <!-- ARCHIVED PRODUCTS TAB -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                    <thead class="text-[11px] uppercase tracking-wider text-slate-400 bg-slate-50/50 dark:bg-dark-850/50 border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Code</th>
                            <th class="py-3 px-4">Product Name</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Vehicle / Brand</th>
                            <th class="py-3 px-4 text-right">Unit Price</th>
                            <th class="py-3 px-4">Removed Date</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($archivedProducts as $product)
                        <tr class="hover:bg-slate-50 dark:hover:bg-dark-800/50 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-rose-500">
                                {{ $product->product_code }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $product->name }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 dark:bg-dark-800 text-slate-700 dark:text-slate-300">
                                    {{ $product->category->name ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-500">
                                {{ $product->vehicle_brand ?: 'Universal' }}
                            </td>
                            <td class="py-3 px-4 text-right font-display font-bold text-slate-900 dark:text-white">
                                ₱ {{ number_format($product->unit_price, 2) }}
                            </td>
                            <td class="py-3 px-4 text-slate-500">
                                {{ $product->deleted_at ? $product->deleted_at->format('M d, Y h:i A') : 'N/A' }}
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <form method="POST" action="{{ route('products.restore', $product->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-500/25 border border-emerald-300 dark:border-emerald-500/30 text-xs font-bold transition-colors">
                                        <i class="fas fa-rotate-left"></i> Restore Product
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-14 text-center text-slate-500">
                                <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 dark:bg-dark-800 flex items-center justify-center text-2xl text-slate-400 mb-3">
                                    <i class="fas fa-box-archive"></i>
                                </div>
                                <p class="font-bold text-slate-700 dark:text-slate-300 text-sm">No archived products in trash</p>
                                <p class="text-xs text-slate-400 mt-1">All catalog items are currently active in inventory and available in POS.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($archivedProducts->hasPages())
            <div class="mt-4 pt-3 border-t border-slate-200 dark:border-slate-800">
                {{ $archivedProducts->links('vendor.pagination.custom') }}
            </div>
            @endif

            @else
            <!-- ARCHIVED CUSTOMERS TAB -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                    <thead class="text-[11px] uppercase tracking-wider text-slate-400 bg-slate-50/50 dark:bg-dark-850/50 border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Client Name</th>
                            <th class="py-3 px-4">Contact #</th>
                            <th class="py-3 px-4">Vehicle &amp; Plate</th>
                            <th class="py-3 px-4">Address</th>
                            <th class="py-3 px-4 text-center">Past Orders</th>
                            <th class="py-3 px-4">Removed Date</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($archivedCustomers as $cust)
                        <tr class="hover:bg-slate-50 dark:hover:bg-dark-800/50 transition-colors">
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 dark:text-white">{{ $cust->name }}</div>
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-600 dark:text-slate-400">
                                {{ $cust->contact_number ?: 'None' }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-slate-800 dark:text-slate-200">{{ $cust->vehicle_make_model ?: 'No vehicle' }}</div>
                                @if($cust->plate_number)
                                <div class="font-mono text-[11px] text-slate-400">Plate: {{ $cust->plate_number }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-500 max-w-[200px] truncate">
                                {{ $cust->address ?: 'None' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-cyan-50 dark:bg-cyan-500/15 text-cyan-600 dark:text-cyan-400 border border-cyan-200 dark:border-cyan-500/30">
                                    {{ $cust->orders_count }} Orders
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-500">
                                {{ $cust->deleted_at ? $cust->deleted_at->format('M d, Y h:i A') : 'N/A' }}
                            </td>
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <form method="POST" action="{{ route('customers.restore', $cust->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-500/25 border border-emerald-300 dark:border-emerald-500/30 text-xs font-bold transition-colors">
                                        <i class="fas fa-rotate-left"></i> Restore Customer
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-14 text-center text-slate-500">
                                <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 dark:bg-dark-800 flex items-center justify-center text-2xl text-slate-400 mb-3">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                                <p class="font-bold text-slate-700 dark:text-slate-300 text-sm">No archived customer records in trash</p>
                                <p class="text-xs text-slate-400 mt-1">All client profiles and vehicle histories are active.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($archivedCustomers->hasPages())
            <div class="mt-4 pt-3 border-t border-slate-200 dark:border-slate-800">
                {{ $archivedCustomers->links('vendor.pagination.custom') }}
            </div>
            @endif
            @endif
        </div>
    </div>
</div>
@endsection
