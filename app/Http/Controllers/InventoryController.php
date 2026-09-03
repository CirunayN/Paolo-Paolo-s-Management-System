<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Category;
use App\Models\StockTransaction;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::with(['product.category']);

        if ($request->filled('status')) {
            if ($request->status === 'low') {
                $query->whereColumn('quantity_on_hand', '<=', 'reorder_level')
                      ->where('quantity_on_hand', '>', 0);
            } elseif ($request->status === 'out') {
                $query->where('quantity_on_hand', '<=', 0);
            } elseif ($request->status === 'in') {
                $query->whereColumn('quantity_on_hand', '>', 'reorder_level');
            }
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('product', function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('product_code', 'like', "%{$s}%")
                  ->orWhere('vehicle_model', 'like', "%{$s}%")
                  ->orWhere('vehicle_brand', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        $inventories = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        // Statistics
        $totalItemsCount = Inventory::count();
        $lowStockCount = Inventory::whereColumn('quantity_on_hand', '<=', 'reorder_level')->where('quantity_on_hand', '>', 0)->count();
        $outOfStockCount = Inventory::where('quantity_on_hand', '<=', 0)->count();

        return view('inventory.index', compact('inventories', 'categories', 'totalItemsCount', 'lowStockCount', 'outOfStockCount'));
    }

    public function adjust(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'new_quantity' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255',
        ]);

        $oldQty = $inventory->quantity_on_hand;
        $newQty = floatval($validated['new_quantity']);
        $diff = $newQty - $oldQty;

        $inventory->quantity_on_hand = $newQty;
        $inventory->save();

        StockTransaction::create([
            'product_id' => $inventory->product_id,
            'user_id' => Auth::id(),
            'type' => 'adjustment',
            'quantity' => $diff,
            'balance_after' => $newQty,
            'reference_no' => 'MANUAL-ADJUST',
            'remarks' => "Stock adjustment: {$validated['reason']} (Was {$oldQty}, set to {$newQty})",
        ]);

        return redirect()->back()->with('success', "Stock for '{$inventory->product->name}' updated to {$newQty}.");
    }

    public function transactions(Request $request)
    {
        $query = StockTransaction::with(['product', 'user'])->latest();

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->paginate(20)->withQueryString();
        $products = Product::orderBy('name')->get();

        return view('inventory.transactions', compact('transactions', 'products'));
    }
}
