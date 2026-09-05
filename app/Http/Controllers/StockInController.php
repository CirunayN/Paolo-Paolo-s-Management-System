<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\StockIn;
use App\Models\StockInItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\StockTransaction;

class StockInController extends Controller
{
    public function index()
    {
        $stockIns = StockIn::with(['supplier', 'user', 'items.product'])
            ->latest('received_date')
            ->latest('id')
            ->paginate(15);

        return view('stock_in.index', compact('stockIns'));
    }

    public function create()
    {
        $products = Product::with('inventory')->where('is_active', true)->orderBy('name')->get();
        $recentSources = StockIn::whereNotNull('source')->distinct()->pluck('source');

        // Generate auto reference number: STK-YYYYMMDD-XXXX
        $datePrefix = date('Ymd');
        $latest = StockIn::whereDate('created_at', today())->latest()->first();
        $seq = $latest ? (intval(substr($latest->reference_no, -4)) + 1) : 1;
        $autoRef = 'STK-' . $datePrefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

        return view('stock_in.create', compact('products', 'recentSources', 'autoRef'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference_no' => 'required|string|max:50|unique:stock_ins,reference_no',
            'source' => 'nullable|string|max:150',
            'received_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_received' => 'required|numeric|min:0.1',
            'items.*.cost_per_unit' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $user = Auth::user();

            // Calculate total cost
            $totalCost = 0;
            foreach ($validated['items'] as $item) {
                $totalCost += ($item['quantity_received'] * $item['cost_per_unit']);
            }

            // 1. Create Stock-In Header
            $stockIn = StockIn::create([
                'reference_no' => strtoupper($validated['reference_no']),
                'source' => $validated['source'] ?: 'General Wholesaler / Spot Importer',
                'user_id' => $user->id,
                'total_cost' => $totalCost,
                'notes' => $validated['notes'] ?? null,
                'received_date' => $validated['received_date'],
            ]);

            // 2. Insert line items and AUTOMATICALLY UPDATE INVENTORY MODULE
            foreach ($validated['items'] as $item) {
                $qty = floatval($item['quantity_received']);
                $cost = floatval($item['cost_per_unit']);
                $subtotal = $qty * $cost;

                StockInItem::create([
                    'stock_in_id' => $stockIn->id,
                    'product_id' => $item['product_id'],
                    'quantity_received' => $qty,
                    'cost_per_unit' => $cost,
                    'subtotal' => $subtotal,
                ]);

                // Update product latest cost price
                $product = Product::findOrFail($item['product_id']);
                if ($cost > 0) {
                    $product->cost_price = $cost;
                    $product->save();
                }

                // DIRECT UPDATE TO INVENTORY TABLE
                $inventory = Inventory::firstOrCreate(
                    ['product_id' => $item['product_id']],
                    ['quantity_on_hand' => 0, 'reorder_level' => $product->stock_alert_level]
                );

                $inventory->quantity_on_hand += $qty;
                $inventory->last_restocked_at = now();
                $inventory->save();

                // Create audit trail entry
                StockTransaction::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'type' => 'stock_in',
                    'quantity' => $qty,
                    'balance_after' => $inventory->quantity_on_hand,
                    'reference_no' => $stockIn->reference_no,
                    'remarks' => "Received from Stock-In #{$stockIn->reference_no}",
                ]);
            }

            return redirect()->route('stock-in.index')
                ->with('success', "Stock-In #{$stockIn->reference_no} recorded successfully! Inventory quantities updated.");
        });
    }

    public function show(StockIn $stockIn)
    {
        $stockIn->load(['supplier', 'user', 'items.product']);
        return view('stock_in.show', compact('stockIn'));
    }
}
