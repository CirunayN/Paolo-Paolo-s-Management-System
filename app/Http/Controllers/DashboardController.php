<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Inventory;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Key Metrics
        $todaySales = Order::whereDate('created_at', $today)->sum('total_amount');
        $todayOrdersCount = Order::whereDate('created_at', $today)->count();

        $monthSales = Order::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');

        $lowStockCount = Inventory::whereColumn('quantity_on_hand', '<=', 'reorder_level')->count();
        $totalProducts = Product::where('is_active', true)->count();

        // Recent Orders
        $recentOrders = Order::with(['user', 'customer'])
            ->latest()
            ->take(8)
            ->get();

        // Low stock products alert list
        $lowStockProducts = Product::with('inventory')
            ->whereHas('inventory', function ($q) {
                $q->whereColumn('quantity_on_hand', '<=', 'reorder_level');
            })
            ->take(5)
            ->get();

        // Top Selling Products
        $topProducts = OrderItem::select('product_name', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_name')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // 7-day Sales Chart Data for ECharts
        $salesDates = [];
        $salesAmounts = [];
        $salesOrders = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $salesDates[] = $date->format('M d (D)');

            $dailyTotal = Order::whereDate('created_at', $dateStr)->sum('total_amount');
            $dailyCount = Order::whereDate('created_at', $dateStr)->count();

            $salesAmounts[] = (float)$dailyTotal;
            $salesOrders[] = $dailyCount;
        }

        // Category breakdown for ECharts
        $categoryBreakdown = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name as name', DB::raw('SUM(order_items.subtotal) as value'))
            ->groupBy('categories.name')
            ->orderByDesc('value')
            ->get();

        return view('dashboard', compact(
            'todaySales',
            'todayOrdersCount',
            'monthSales',
            'lowStockCount',
            'totalProducts',
            'recentOrders',
            'lowStockProducts',
            'topProducts',
            'salesDates',
            'salesAmounts',
            'salesOrders',
            'categoryBreakdown'
        ));
    }
}
