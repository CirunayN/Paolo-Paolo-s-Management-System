@extends('layouts.app')

@section('content')
<div class="space-y-6" data-auto-animate>
    <!-- Welcome Header & Quick Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-5 bg-white dark:bg-gradient-to-r dark:from-dark-850 dark:via-dark-800 dark:to-dark-850 p-6 sm:p-7 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-cyan-600 dark:text-cyan-400 mb-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                <span>Automotive Store Live Overview</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-bold font-display text-slate-900 dark:text-white">
                Welcome back, {{ auth()->user()->name }}!
            </h2>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('pos.index') }}" class="flex items-center gap-2.5 px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold text-sm shadow-lg shadow-cyan-500/20 transition-all transform hover:-translate-y-0.5">
                <i class="fas fa-cash-register text-base"></i>
                <span>Open POS Terminal</span>
            </a>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('backup.index') }}" class="flex items-center gap-2 px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-dark-700 dark:hover:bg-dark-600 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-sm transition-all">
                <i class="fas fa-hard-drive text-cyan-500"></i>
                <span>Backups</span>
            </a>
            @endif
        </div>
    </div>

    <!-- 4 Primary KPI Cards (Enlarged, Clear & Accessible) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5" data-auto-animate>
        <!-- Today's Sales -->
        <div class="glass-card rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm hover:border-cyan-500/40 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Today's Sales</span>
                <div class="w-12 h-12 rounded-xl bg-cyan-500/15 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-xl">
                    <i class="fas fa-peso-sign"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-black font-display text-slate-900 dark:text-white tracking-tight">₱ {{ number_format($todaySales, 2) }}</div>
                <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 mt-2">
                    <span class="text-emerald-600 dark:text-emerald-400 font-bold"><i class="fas fa-receipt mr-1"></i>{{ $todayOrdersCount }}</span> orders completed today
                </div>
            </div>
        </div>

        <!-- Today's Orders Count -->
        <div class="glass-card rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm hover:border-emerald-500/40 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Today's Transactions</span>
                <div class="w-12 h-12 rounded-xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl">
                    <i class="fas fa-cart-shopping"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-black font-display text-slate-900 dark:text-white tracking-tight">{{ $todayOrdersCount }}</div>
                <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 mt-2">
                    <span>Processed across cashier terminals</span>
                </div>
            </div>
        </div>

        <!-- Monthly Sales -->
        <div class="glass-card rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm hover:border-blue-500/40 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">This Month's Sales</span>
                <div class="w-12 h-12 rounded-xl bg-blue-500/15 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-black font-display text-slate-900 dark:text-white tracking-tight">₱ {{ number_format($monthSales, 2) }}</div>
                <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 mt-2">
                    <span>Total revenue for {{ date('F Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="glass-card rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm hover:border-amber-500/40 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Low Stock Warnings</span>
                <div class="w-12 h-12 rounded-xl bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-3xl font-black font-display {{ $lowStockCount > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-800 dark:text-slate-200' }} tracking-tight">
                    {{ $lowStockCount }} Items
                </div>
                <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 mt-2">
                    <a href="{{ route('inventory.index') }}" class="text-cyan-600 dark:text-cyan-400 font-semibold hover:underline">
                        View stock adjustments &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- UNIQUE EXECUTIVE FEATURE: Inventory Valuation & Capital Breakdown -->
    <div class="glass-card rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm bg-gradient-to-r from-slate-50 via-white to-slate-50 dark:from-dark-850 dark:via-dark-800 dark:to-dark-850">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5 pb-4 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h3 class="text-lg font-bold font-display text-slate-900 dark:text-white flex items-center gap-2.5">
                    <i class="fas fa-vault text-cyan-500"></i> Inventory Asset Valuation &amp; Capital Reserve
                </h3>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('products.index') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-200 dark:bg-dark-700 hover:bg-slate-300 dark:hover:bg-dark-600 text-slate-700 dark:text-slate-200 font-bold text-xs transition-colors">
                    <i class="fas fa-boxes-stacked mr-1 text-cyan-500"></i> Open Inventory
                </a>
                <a href="{{ route('inventory.index') }}" class="px-3.5 py-1.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 border border-cyan-500/30 text-cyan-600 dark:text-cyan-400 font-bold text-xs transition-colors">
                    <i class="fas fa-clipboard-check mr-1"></i> Stock Adjustments
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Total Capital Invested (Cost Price Basis) -->
            <div class="p-4 rounded-xl bg-white dark:bg-dark-900/80 border border-slate-200 dark:border-slate-800">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Capital Invested (Cost)</span>
                <div class="text-2xl font-black font-display text-slate-900 dark:text-white mt-1.5">
                    ₱ {{ number_format($totalCostValue, 2) }}
                </div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Wholesale acquisition value</div>
            </div>

            <!-- Retail Valuation (Gross Potential) -->
            <div class="p-4 rounded-xl bg-white dark:bg-dark-900/80 border border-slate-200 dark:border-slate-800">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Retail Worth (Gross)</span>
                <div class="text-2xl font-black font-display text-cyan-600 dark:text-cyan-400 mt-1.5">
                    ₱ {{ number_format($totalRetailValue, 2) }}
                </div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Gross sales potential</div>
            </div>

            <!-- Potential Gross Profit -->
            <div class="p-4 rounded-xl bg-white dark:bg-dark-900/80 border border-slate-200 dark:border-slate-800">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Projected Gross Profit</span>
                <div class="text-2xl font-black font-display text-emerald-600 dark:text-emerald-400 mt-1.5">
                    ₱ {{ number_format($potentialProfit, 2) }}
                </div>
                <div class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold mt-1">
                    <i class="fas fa-arrow-trend-up mr-0.5"></i> {{ $profitMargin }}% avg profit margin
                </div>
            </div>

            <!-- Units on Shelves -->
            <div class="p-4 rounded-xl bg-white dark:bg-dark-900/80 border border-slate-200 dark:border-slate-800">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Units on Shelves</span>
                <div class="text-2xl font-black font-display text-slate-900 dark:text-white mt-1.5">
                    {{ number_format($totalUnitsOnHand) }} <span class="text-sm font-normal text-slate-400">units</span>
                </div>
                <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">Across {{ $totalProducts }} active products</div>
            </div>
        </div>
    </div>

    <!-- ECharts Section: 7-Day Trends & Category Donut -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sales Trend Interactive Chart (2 Columns) -->
        <div class="lg:col-span-2 glass-card rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold font-display text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-chart-area text-cyan-500"></i> 7-Day Revenue &amp; Order Volume
                    </h3>
                </div>
                <span class="text-xs bg-slate-100 dark:bg-dark-700 text-slate-600 dark:text-slate-300 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 font-semibold">Live Daily Feed</span>
            </div>
            <div id="salesTrendChart" style="height: 330px; width: 100%;"></div>
        </div>

        <!-- Category Distribution Chart (1 Column) -->
        <div class="glass-card rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold font-display text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-chart-pie text-cyan-500"></i> Sales by Category
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Revenue share across matting types</p>
                </div>
            </div>
            <div id="categoryChart" style="height: 330px; width: 100%;"></div>
        </div>
    </div>

    <!-- Bottom Row: Recent Orders & Stock Status -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" data-auto-animate>
        <!-- Recent Orders (2 Columns) -->
        <div class="lg:col-span-2 glass-card rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold font-display text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-clock-rotate-left text-cyan-500"></i> Recent Sales Transactions
                </h3>
                <a href="{{ route('customers.index') }}" class="text-xs font-semibold text-cyan-600 dark:text-cyan-400 hover:underline">Customer Records &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800 uppercase tracking-wider text-xs font-bold">
                            <th class="py-3 px-3">Invoice #</th>
                            <th class="py-3 px-3">Customer / Vehicle</th>
                            <th class="py-3 px-3">Type</th>
                            <th class="py-3 px-3">Payment</th>
                            <th class="py-3 px-3 text-right">Total</th>
                            <th class="py-3 px-3 text-center">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        @forelse($recentOrders as $order)
                        <tr class="hover:bg-slate-50 dark:hover:bg-dark-800/50 transition-colors">
                            <td class="py-3.5 px-3 font-bold font-mono text-cyan-600 dark:text-cyan-400">{{ $order->invoice_no }}</td>
                            <td class="py-3.5 px-3">
                                <div class="font-semibold text-slate-900 dark:text-slate-200">{{ $order->customer_name ?: ($order->customer ? $order->customer->name : 'Walk-in') }}</div>
                                @if($order->vehicle_details)
                                <div class="text-xs text-slate-500 dark:text-slate-400">{{ $order->vehicle_details }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $order->order_type == 'With Installation' ? 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300' : 'bg-slate-100 dark:bg-slate-700/50 text-slate-700 dark:text-slate-300' }}">
                                    {{ $order->order_type }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3 font-medium text-slate-700 dark:text-slate-300">
                                {{ $order->payment_method }}
                            </td>
                            <td class="py-3.5 px-3 text-right font-black font-display text-slate-900 dark:text-white text-base">
                                ₱ {{ number_format($order->total_amount, 2) }}
                            </td>
                            <td class="py-3.5 px-3 text-center">
                                <a href="{{ route('pos.receipt', $order->id) }}" target="_blank" class="text-slate-400 hover:text-cyan-500 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-dark-700 transition-colors">
                                    <i class="fas fa-print"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">No transactions recorded yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Stock Intelligence Alert Box (1 Column) -->
        <div class="glass-card rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold font-display text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-boxes-stacked text-cyan-500"></i> Stock Intelligence
                    </h3>
                    <a href="{{ route('inventory.index') }}" class="text-xs font-semibold text-cyan-600 dark:text-cyan-400 hover:underline">Full Inventory &rarr;</a>
                </div>

                <!-- Tab Buttons -->
                <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-dark-900 rounded-xl mb-3 text-xs font-bold">
                    <button type="button" id="tabBtnLow" onclick="switchStockTab('low')"
                        class="flex-1 py-1.5 px-2.5 rounded-lg transition-all bg-white dark:bg-dark-800 text-amber-600 dark:text-amber-400 shadow-sm">
                        <i class="fas fa-triangle-exclamation mr-1"></i> Low Stock ({{ $lowStockCount }})
                    </button>
                    <button type="button" id="tabBtnDead" onclick="switchStockTab('dead')"
                        class="flex-1 py-1.5 px-2.5 rounded-lg transition-all text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                        <i class="fas fa-bed mr-1"></i> Stagnant ({{ $deadStockProducts->count() }})
                    </button>
                </div>

                <!-- Tab 1: Low Stock Alert List -->
                <div id="stockPanelLow" class="space-y-3" data-auto-animate>
                    @forelse($lowStockProducts as $prod)
                    <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-dark-800/60 border border-amber-300 dark:border-amber-500/20 flex items-center justify-between">
                        <div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white truncate max-w-[160px]">{{ $prod->name }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $prod->vehicle_model }}</div>
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-2.5 py-1 rounded text-xs font-extrabold bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-300 dark:border-amber-500/30">
                                {{ (float)($prod->inventory->quantity_on_hand ?? 0) }} {{ $prod->unit_of_measure }}
                            </span>
                            <div class="text-[10px] text-slate-400 mt-0.5">Min: {{ $prod->stock_alert_level }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-500 text-sm">
                        <i class="fas fa-check-double text-emerald-500 text-3xl mb-2"></i>
                        <div>All physical matting inventory is healthy!</div>
                    </div>
                    @endforelse
                </div>

                <!-- Tab 2: Stagnant / Dead Stock (0 Sales in 30 Days) -->
                <div id="stockPanelDead" class="space-y-3 hidden" data-auto-animate>
                    @forelse($deadStockProducts as $prod)
                    <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-dark-800/60 border border-rose-300/60 dark:border-rose-500/20 flex items-center justify-between">
                        <div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white truncate max-w-[160px]">{{ $prod->name }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $prod->vehicle_model }}</div>
                            <div class="text-[10px] text-rose-600 dark:text-rose-400 font-semibold mt-0.5">
                                <i class="fas fa-triangle-exclamation mr-0.5"></i> 0 sold in past 30 days
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-2.5 py-1 rounded text-xs font-extrabold bg-slate-200 dark:bg-dark-700 text-slate-800 dark:text-slate-200">
                                {{ (float)($prod->inventory->quantity_on_hand ?? 0) }} sitting
                            </span>
                            <div class="text-[10px] text-slate-400 mt-0.5">₱ {{ number_format($prod->unit_price, 2) }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-500 text-sm">
                        <i class="fas fa-fire-flame-curved text-amber-500 text-3xl mb-2"></i>
                        <div>No dead stock! All stocked items have active sales demand.</div>
                    </div>
                    @endforelse
                </div>
            </div>

            @if(auth()->user()->isAdmin())
            <div class="mt-5 pt-4 border-t border-slate-200 dark:border-slate-800">
                <a href="{{ route('stock-in.create') }}" class="block w-full py-2.5 px-4 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 border border-cyan-500/30 text-cyan-600 dark:text-cyan-400 text-center font-bold text-xs transition-colors">
                    <i class="fas fa-truck-loading mr-1.5"></i> Receive Inward Stock Delivery
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#94a3b8' : '#475569';
    const splitColor = isDark ? 'rgba(51, 65, 85, 0.3)' : 'rgba(226, 232, 240, 0.8)';

    // 1. ECharts 7-Day Sales Trend
    const salesChartEl = document.getElementById('salesTrendChart');
    if (salesChartEl) {
        const salesChart = echarts.init(salesChartEl);
        const dates = {!! json_encode($salesDates) !!};
        const amounts = {!! json_encode($salesAmounts) !!};
        const orders = {!! json_encode($salesOrders) !!};

        const option = {
            backgroundColor: 'transparent',
            tooltip: {
                trigger: 'axis',
                backgroundColor: isDark ? 'rgba(15, 23, 42, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                borderColor: '#38bdf8',
                borderWidth: 1,
                textStyle: { color: isDark ? '#f8fafc' : '#0f172a', fontSize: 13 },
                formatter: function (params) {
                    let res = `<div style="font-weight:bold;margin-bottom:4px;">${params[0].name}</div>`;
                    params.forEach(item => {
                        if (item.seriesName === 'Revenue') {
                            res += `<div style="color:#0284c7;"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#0284c7;margin-right:6px;"></span>₱ ${item.value.toLocaleString('en-US', {minimumFractionDigits: 2})}</div>`;
                        } else {
                            res += `<div style="color:#a855f7;"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#a855f7;margin-right:6px;"></span>${item.value} Orders</div>`;
                        }
                    });
                    return res;
                }
            },
            grid: { left: '3%', right: '4%', bottom: '8%', top: '12%', containLabel: true },
            xAxis: {
                type: 'category',
                data: dates,
                axisLine: { lineStyle: { color: isDark ? '#334155' : '#cbd5e1' } },
                axisLabel: { color: textColor, fontSize: 12 }
            },
            yAxis: [
                {
                    type: 'value',
                    name: 'Revenue (₱)',
                    nameTextStyle: { color: textColor, fontSize: 11 },
                    axisLine: { lineStyle: { color: isDark ? '#334155' : '#cbd5e1' } },
                    splitLine: { lineStyle: { color: splitColor } },
                    axisLabel: {
                        color: textColor,
                        formatter: val => '₱' + (val >= 1000 ? (val / 1000) + 'k' : val)
                    }
                },
                {
                    type: 'value',
                    name: 'Orders',
                    nameTextStyle: { color: textColor, fontSize: 11 },
                    minInterval: 1,
                    splitLine: { show: false },
                    axisLabel: { color: textColor }
                }
            ],
            series: [
                {
                    name: 'Revenue',
                    type: 'line',
                    smooth: true,
                    data: amounts,
                    lineStyle: { width: 3.5, color: '#0284c7' },
                    itemStyle: { color: '#0284c7' },
                    areaStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: 'rgba(56, 189, 248, 0.45)' },
                            { offset: 1, color: 'rgba(56, 189, 248, 0.0)' }
                        ])
                    }
                },
                {
                    name: 'Orders',
                    type: 'bar',
                    yAxisIndex: 1,
                    data: orders,
                    barWidth: '24%',
                    itemStyle: {
                        color: 'rgba(168, 85, 247, 0.7)',
                        borderRadius: [6, 6, 0, 0]
                    }
                }
            ]
        };

        salesChart.setOption(option);
        window.addEventListener('resize', () => salesChart.resize());
    }

    // 2. ECharts Category Donut
    const catChartEl = document.getElementById('categoryChart');
    if (catChartEl) {
        const catChart = echarts.init(catChartEl);
        const catData = {!! json_encode($categoryBreakdown) !!};

        const displayData = catData.length > 0 ? catData : [
            { name: 'Deep Dish Matting', value: 45000 },
            { name: '5D Diamond Matting', value: 28000 },
            { name: 'Coil & Rubber', value: 16000 },
            { name: 'Trunk Trays', value: 12000 },
            { name: 'Motorcycle Mats', value: 8500 }
        ];

        const catOption = {
            backgroundColor: 'transparent',
            tooltip: {
                trigger: 'item',
                backgroundColor: isDark ? 'rgba(15, 23, 42, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                borderColor: '#38bdf8',
                textStyle: { color: isDark ? '#f8fafc' : '#0f172a', fontSize: 13 },
                formatter: params => `${params.name}<br/><b>₱ ${Number(params.value).toLocaleString('en-US', {minimumFractionDigits: 2})}</b> (${params.percent}%)`
            },
            legend: {
                bottom: '0%',
                left: 'center',
                textStyle: { color: textColor, fontSize: 11 },
                itemWidth: 12,
                itemHeight: 12
            },
            series: [
                {
                    name: 'Sales Category',
                    type: 'pie',
                    radius: ['45%', '70%'],
                    center: ['50%', '42%'],
                    avoidLabelOverlap: false,
                    itemStyle: {
                        borderRadius: 6,
                        borderColor: isDark ? '#0f172a' : '#ffffff',
                        borderWidth: 2
                    },
                    label: { show: false },
                    emphasis: {
                        label: { show: true, fontSize: 13, fontWeight: 'bold' }
                    },
                    data: displayData,
                    color: ['#0284c7', '#0ea5e9', '#6366f1', '#a855f7', '#ec4899', '#f59e0b', '#10b981']
                }
            ]
        };

        catChart.setOption(catOption);
        window.addEventListener('resize', () => catChart.resize());
    }
});

function switchStockTab(tab) {
    const tabLow = document.getElementById('tabBtnLow');
    const tabDead = document.getElementById('tabBtnDead');
    const panelLow = document.getElementById('stockPanelLow');
    const panelDead = document.getElementById('stockPanelDead');

    if (!tabLow || !tabDead || !panelLow || !panelDead) return;

    if (tab === 'low') {
        panelLow.classList.remove('hidden');
        panelDead.classList.add('hidden');
        tabLow.className = 'flex-1 py-1.5 px-2.5 rounded-lg transition-all bg-white dark:bg-dark-800 text-amber-600 dark:text-amber-400 shadow-sm font-bold';
        tabDead.className = 'flex-1 py-1.5 px-2.5 rounded-lg transition-all text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-bold';
    } else {
        panelLow.classList.add('hidden');
        panelDead.classList.remove('hidden');
        tabDead.className = 'flex-1 py-1.5 px-2.5 rounded-lg transition-all bg-white dark:bg-dark-800 text-rose-600 dark:text-rose-400 shadow-sm font-bold';
        tabLow.className = 'flex-1 py-1.5 px-2.5 rounded-lg transition-all text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-bold';
    }
}
</script>
@endpush
