<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'inventory']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('product_code', 'like', "%{$s}%")
                  ->orWhere('vehicle_model', 'like', "%{$s}%")
                  ->orWhere('vehicle_brand', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('vehicle_brand')) {
            $query->where('vehicle_brand', $request->vehicle_brand);
        }

        $products = $query->orderBy('name')->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();
        $brands = Product::whereNotNull('vehicle_brand')->where('vehicle_brand', '!=', '')->distinct()->pluck('vehicle_brand');

        return view('products.index', compact('products', 'categories', 'brands'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_code' => 'required|string|max:50|unique:products,product_code',
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'vehicle_brand' => 'nullable|string|max:50',
            'vehicle_model' => 'nullable|string|max:100',
            'material_type' => 'nullable|string|max:100',
            'unit_of_measure' => 'required|string|max:20',
            'cost_price' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'stock_alert_level' => 'required|integer|min:0',
            'initial_stock' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            $uploadDir = public_path('images/products');
            if (!File::exists($uploadDir)) {
                File::makeDirectory($uploadDir, 0777, true, true);
            }

            foreach (array_slice($request->file('images'), 0, 5) as $file) {
                $filename = time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDir, $filename);
                $imagePaths[] = 'images/products/' . $filename;
            }
        }

        $product = Product::create([
            'product_code' => strtoupper($validated['product_code']),
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'vehicle_brand' => $validated['vehicle_brand'] ?? 'Universal',
            'vehicle_model' => $validated['vehicle_model'] ?? 'Universal',
            'material_type' => $validated['material_type'] ?? null,
            'unit_of_measure' => $validated['unit_of_measure'],
            'cost_price' => $validated['cost_price'],
            'unit_price' => $validated['unit_price'],
            'stock_alert_level' => $validated['stock_alert_level'],
            'image_path' => $imagePaths[0] ?? null,
            'images' => $imagePaths,
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        $initialStock = floatval($request->input('initial_stock', 0));
        Inventory::create([
            'product_id' => $product->id,
            'quantity_on_hand' => $initialStock,
            'reorder_level' => $product->stock_alert_level,
            'last_restocked_at' => $initialStock > 0 ? now() : null,
        ]);

        if ($initialStock > 0) {
            StockTransaction::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'type' => 'stock_in',
                'quantity' => $initialStock,
                'balance_after' => $initialStock,
                'reference_no' => 'INITIAL-STOCK',
                'remarks' => 'Initial stock on product creation',
            ]);
        }

        return redirect()->route('products.index')->with('success', "Product '{$product->name}' created successfully!");
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_code' => 'required|string|max:50|unique:products,product_code,' . $product->id,
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'vehicle_brand' => 'nullable|string|max:50',
            'vehicle_model' => 'nullable|string|max:100',
            'material_type' => 'nullable|string|max:100',
            'unit_of_measure' => 'required|string|max:20',
            'cost_price' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'stock_alert_level' => 'required|integer|min:0',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
        ]);

        $currentImages = is_array($product->images) ? $product->images : ($product->image_path ? [$product->image_path] : []);

        if ($request->hasFile('images')) {
            $uploadDir = public_path('images/products');
            if (!File::exists($uploadDir)) {
                File::makeDirectory($uploadDir, 0777, true, true);
            }

            $newPaths = [];
            foreach (array_slice($request->file('images'), 0, 5) as $file) {
                $filename = time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDir, $filename);
                $newPaths[] = 'images/products/' . $filename;
            }
            $currentImages = array_slice(array_merge($newPaths, $currentImages), 0, 5);
        }

        $validated['images'] = $currentImages;
        $validated['image_path'] = $currentImages[0] ?? null;
        $validated['is_active'] = $request->has('is_active');

        $product->update($validated);

        if ($product->inventory) {
            $product->inventory->update([
                'reorder_level' => $product->stock_alert_level,
            ]);
        }

        return redirect()->route('products.index')->with('success', "Product '{$product->name}' updated successfully!");
    }

    public function destroy(Product $product)
    {
        $name = $product->name;
        $product->delete();
        return redirect()->route('products.index')->with('success', "Product '{$name}' deleted successfully!");
    }
}
